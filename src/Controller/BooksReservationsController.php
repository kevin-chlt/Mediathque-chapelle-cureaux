<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\BooksReservations;
use App\Repository\BooksRepository;
use App\Repository\BooksReservationsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

            $this->addFlash('success', 'Vous pouvez venir chercher votre livre dès maintenant à la médiathèque');
        }

        return $this->render('books/admin-index.html.twig', [
            'books' => $booksRepository->findAll()
        ]);
    }

    /**
     * @Route("/", name="user_books_reservations")
     */
    public function getUserReservation (BooksReservationsRepository $reservationsRepository) : Response
    {
        $reservations = $reservationsRepository->findBy(['user' => $this->getUser()->getId()]);

        return $this->render('books_reservations/user-index.html.twig', [
            'reservations' => $reservations
        ]);
    }

    /**
     * @Route("/cancel-reservation", name="cancel-reservation")
     */
    public function cancelReservationByUser (BooksReservationsRepository $reservationsRepository) : Response
    {
        $reservations = $reservationsRepository->findBy(['user' => $this->getUser()->getId()]);

        return $this->redirectToRoute('user_books_reservations', [
            'reservations' => $reservations
        ]);
    }

    /**
     * @Route("/administration", name="books_reservations_index", methods={"GET"})
     * @IsGranted("ROLE_EMPLOYEE", message="Vous n'êtes pas autorisé à acceder à cette page")
     */
    public function index(BooksReservationsRepository $booksReservationsRepository, EntityManagerInterface $entityManager): Response
    {
        // Check the date and delete the reservation not recolted since 3 days
        $books = $booksReservationsRepository->findAll();

        foreach($books as $book) {
            $reservedAt  = new \DateTime($book->getReservedAt()->format('Y-m-d H:m:s'));
            $date_now = new \DateTime();
            if(!$book->getIsCollected() && ($reservedAt->add(new \DateInterval('P3D')) < $date_now)){
                $entityManager->remove($book);
                $entityManager->flush();
            }
        }

        return $this->render('books_reservations/admin-index.html.twig', [
            'reservations' => $booksReservationsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/customer-collect/{id}", name="books-update-collect")
     * @IsGranted("ROLE_EMPLOYEE", message="Vous n'êtes pas autorisé à effectué cette action")
     */
    // Change the collected status of book when employee want it
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
