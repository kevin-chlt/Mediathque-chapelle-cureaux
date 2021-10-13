<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\BooksReservations;
use App\Form\FiltersType;
use App\Repository\BooksRepository;
use App\Repository\BooksReservationsRepository;
use App\Repository\UsersRepository;
use App\Services\OutdatedReservations;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Route("/reservations")
 */
class BooksReservationsController extends AbstractController
{
    /**
     * @Route("/new/{id}", name="books_reservations_new", methods={"GET"})
     */
    # Get Book with Params ID, make the reservation
    public function new(Books $book, UsersRepository $usersRepository, BooksRepository $booksRepository): Response
    {
        $user = $usersRepository->find($this->getUser()->getId());
        $filterForm = $this->createForm(FiltersType::class);

        if(!$book->getIsFree()) {
            $this->addFlash('errors', 'Le livre n\'est pas disponible');
        }

        if($user->getBooksReservations()->count() > 10) {
            $this->addFlash('errors', 'Vous avez déjà atteint la limite de livre emprunté');
        }

        if($book->getIsFree() && $user->getBooksReservations()->count() < 10) {
            $booksReservation = new BooksReservations();
            $booksReservation->setBooks($book)
                ->setUser($user);

            $book->setIsFree(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booksReservation);
            $entityManager->flush();

            $this->addFlash('success', 'Vous pouvez venir chercher votre livre dès maintenant à la médiathèque.');
        }

        return $this->render('books/index.html.twig', [
            'books' => $booksRepository->findAll(),
            'filterForm' => $filterForm->createView()
        ]);
    }

    /**
     * @Route("/", name="user_books_reservations")
     */
    // Get user reservations
    public function getUserReservation (BooksReservationsRepository $reservationsRepository, OutdatedReservations $outdatedReservations) : Response
    {
        $reservations = $reservationsRepository->findBy(['user' => $this->getUser()->getId()]);
        $outdatedReservations = $outdatedReservations->getOutdatedReservation($reservations);

        return $this->render('books_reservations/user-index.html.twig', [
            'reservations' => $reservations,
            'outdatedReservations' => $outdatedReservations
        ]);
    }

    /**
     * @Route("/cancel-reservation/{id}", name="cancel-reservation")
     */
    // Cancel reservation if user doesn't collect his book
    public function cancelReservationByUser (BooksReservations $booksReservations, BooksReservationsRepository $reservationsRepository, BooksRepository $booksRepository) : Response
    {
        $book = $booksRepository->find($booksReservations->getBooks()->getId());

        if (($this->getUser()->getId() === $booksReservations->getUser()->getId()) && !$booksReservations->getIsCollected() ) {
            $book->setIsFree(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booksReservations);
            $entityManager->flush();

            $this->addFlash('success', 'Votre emprunt à bien été annulé');
        } else {
            throw new AccessDeniedException("Vous n'avez pas le droit d'annuler cette emprunt");
        }


        return $this->redirectToRoute('user_books_reservations', [
            'reservations' => $reservationsRepository->findBy(['user' => $this->getUser()->getId()])
        ]);
    }

    /**
     * @Route("/administration", name="books_reservations_index", methods={"GET"})
     * @IsGranted("ROLE_EMPLOYEE", message="Vous n'êtes pas autorisé à acceder à cette page")
     */
    // Get all the reservation | Admin panel
    public function index(BooksRepository $booksRepository, BooksReservationsRepository $booksReservationsRepository, OutdatedReservations $outdatedReservations): Response
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
            'reservations' => $booksReservationsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/customer-collect/{id}", name="books-update-collect")
     * @IsGranted("ROLE_EMPLOYEE", message="Vous n'êtes pas autorisé à effectué cette action")
     */
    // Change the collected status of book when employee do it
    public function changeCollectStatus (BooksReservations $booksReservations) : Response
    {
        $booksReservations->getIsCollected() ?
            $booksReservations->setIsCollected(false) : $booksReservations->setIsCollected(true);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'Mise à jour du statut effectué avec succès');

        return $this->redirectToRoute('books_reservations_index');
    }

    /**
     * @Route("/remove/{id}", name="books_reservations_delete", methods={"POST"})
     * @IsGranted("ROLE_EMPLOYEE", message="Vous n'êtes pas autorisé à effectué cette action")
     */
    // Delete the reservation and set IsFree of the book at true
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
