<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\BooksReservations;
use App\Repository\BooksRepository;
use App\Repository\BooksReservationsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservations")
 */
class BooksReservationsController extends AbstractController
{
    /**
     * @Route("/", name="books_reservations_index", methods={"GET"})
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

        return $this->render('books_reservations/index.html.twig', [
            'reservations' => $booksReservationsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="books_reservations_new", methods={"GET"})
     */
    # Get Book with Params ID and make the reservation
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

        return $this->render('books/index.html.twig', [
            'books' => $booksRepository->findAll()
        ]);
    }

    /**
     * @Route("/remove/{id}", name="books_reservations_delete", methods={"POST"})
     */
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

    /**
     * @Route("/customer-collect/{id}", name="books-update-collect")
     */
    public function changeCollectStatus (BooksReservations $booksReservations) : Response
    {
        $booksReservations->getIsCollected() ?
            $booksReservations->setIsCollected(false) : $booksReservations->setIsCollected(true);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'Mise à jour du statut effectué ave succès');

        return $this->redirectToRoute('books_reservations_index');
    }
}
