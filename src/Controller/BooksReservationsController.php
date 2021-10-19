<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\BooksReservations;
use App\Repository\BooksRepository;
use App\Repository\BooksReservationsRepository;
use App\Repository\UsersRepository;
use App\Services\OutdatedReservations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/reservations')]
class BooksReservationsController extends AbstractController
{

    // Get Book with Params ID, make the reservation
    #[Route('/new/{id}', name: 'books_reservations_new', methods: ['POST'])]
    public function new(Books $book, UsersRepository $usersRepository, Request $request): Response
    {
        if ($this->isCsrfTokenValid('create'.$book->getId(), $request->request->get('_token'))) {

            $user = $usersRepository->find($this->getUser()->getId());

            if (!$book->getIsFree()) {
                $this->addFlash('errors', 'Le livre n\'est pas disponible');
            }

            if ($user->getBooksReservations()->count() > 10) {
                $this->addFlash('errors', 'Vous avez déjà atteint la limite de livre emprunté');
            }

            if ($book->getIsFree() && $user->getBooksReservations()->count() < 10) {
                $booksReservation = new BooksReservations();
                $booksReservation->setBooks($book)
                    ->setUser($user);

                $book->setIsFree(false);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($booksReservation);
                $entityManager->flush();

                $this->addFlash('success', 'Vous pouvez venir chercher votre livre dès maintenant à la médiathèque.');
            }
        }

        return $this->redirectToRoute('books_show', ['id' => $book->getId()]);
    }


    // Get user reservations
    #[Route('/', name: 'user_books_reservations', methods: ['GET'])]
    public function getUserReservation (BooksReservationsRepository $reservationsRepository, OutdatedReservations $outdatedReservations) : Response
    {
        $reservations = $reservationsRepository->findBy(['user' => $this->getUser()->getId()], ['reservedAt' => 'ASC']);
        $outdatedReservations = $outdatedReservations->getOutdatedReservation($reservations);

        return $this->render('books_reservations/user-index.html.twig', [
            'reservations' => $reservations,
            'outdatedReservations' => $outdatedReservations
        ]);
    }


    // Cancel reservation and set book statut to free
    #[Route('/cancel-reservation/{id}', name: 'books_reservations_cancel', methods: ['POST'])]
    public function cancelReservationByUser (Request $request, BooksReservations $booksReservations, BooksReservationsRepository $reservationsRepository, BooksRepository $booksRepository) : Response
    {
        if ($this->isCsrfTokenValid('cancelReservation'.$booksReservations->getId(), $request->request->get('_token'))) {
            $book = $booksRepository->find($booksReservations->getBooks()->getId());

            if (($this->getUser()->getId() === $booksReservations->getUser()->getId()) && !$booksReservations->getIsCollected()) {
                $book->setIsFree(true);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($booksReservations);
                $entityManager->flush();

                $this->addFlash('success', 'Votre emprunt à bien été annulé');
            } else {
                throw new AccessDeniedException("Vous n'avez pas le droit d'annuler cette emprunt");
            }
        }

        return $this->redirectToRoute('user_books_reservations', [
            'reservations' => $reservationsRepository->findBy(['user' => $this->getUser()->getId()])
        ]);
    }


    // Get all the reservation | Admin panel
    #[Route('/administration', name: 'books_reservations_index', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYEE', message: 'Vous n\'êtes pas autorisé à acceder à cette page')]
    public function adminPanel(BooksReservationsRepository $booksReservationsRepository, OutdatedReservations $outdatedReservations): Response
    {
        // Check the date and delete the reservation not recolted since 3 days
        $reservations = $booksReservationsRepository->findAll();

        $outdatedReservation = $outdatedReservations->getOutdatedReservationAdminPanel($reservations);
        $entityManager = $this->getDoctrine()->getManager();

        foreach($outdatedReservation as $reservation) {
                $reservation->getBooks()->setIsFree(true);
                $entityManager->remove($reservation);
                $entityManager->flush();
        }

        return $this->render('books_reservations/admin-index.html.twig', [
            'reservations' => $booksReservationsRepository->getReservationByDate(),
        ]);
    }

    // Change the collected status of book when employee do it
    #[Route('customer-collect/{id}', name: 'books-update-collect', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE', message: 'Vous n\'êtes pas autorisé à effectué cette action')]
    public function changeCollectStatus (BooksReservations $booksReservations, Request $request) : Response
    {
        if ($this->isCsrfTokenValid('updateCollect'.$booksReservations->getId(), $request->request->get('_token'))) {
            if($booksReservations->getIsCollected()) {
                $booksReservations->setIsCollected(false);
                $booksReservations->setCollectedAt(null);
            } else {
                $booksReservations->setIsCollected(true);
                $booksReservations->setCollectedAt(new \DateTime());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Mise à jour du statut effectué avec succès');
        }

        return $this->redirectToRoute('books_reservations_index');
    }


    // Delete the reservation and set IsFree of the book at true
    #[Route('remove/{id}', name: 'books_reservations_delete', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE', message: 'Vous n\'êtes pas autorisé à effectué cette action')]
    public function delete(Request $request, BooksReservations $booksReservation, BooksRepository $booksRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booksReservation->getId(), $request->request->get('_token'))) {
            $book = $booksRepository->find($booksReservation->getBooks()->getId());

            $book->setIsFree(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booksReservation);
            $entityManager->flush();

            $this->addFlash('success', 'Emprunt clôturé avec succès');
        }

        return $this->redirectToRoute('books_reservations_index', [], Response::HTTP_SEE_OTHER);
    }
}
