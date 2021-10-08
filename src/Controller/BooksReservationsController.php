<?php

namespace App\Controller;

use App\Entity\Books;
use App\Entity\BooksReservations;
use App\Form\BooksReservationsType;
use App\Repository\BooksRepository;
use App\Repository\BooksReservationsRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/books/reservations")
 */
class BooksReservationsController extends AbstractController
{
    /**
     * @Route("/", name="books_reservations_index", methods={"GET"})
     */
    public function index(BooksReservationsRepository $booksReservationsRepository): Response
    {
        return $this->render('books_reservations/index.html.twig', [
            'books_reservations' => $booksReservationsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="books_reservations_new", methods={"GET"})
     */
    # Get Book with Params ID and make the reservation
    public function new(Books $book, UsersRepository $usersRepository, BooksRepository $booksRepository): Response
    {
        $user = $usersRepository->find($this->getUser()->getId());

        if($book->getIsFree()) {
            $booksReservation = new BooksReservations();
            $booksReservation->setBooks($book)
                ->setUser($user);

            $book->setIsFree(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($booksReservation);
            $entityManager->flush();
        } else {
            throw new AccessDeniedException('Le livre demandé est déjà emprunté');
        }

        return $this->renderForm('books/index.html.twig', [
            'books' => $booksRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="books_reservations_delete", methods={"POST"})
     */
    public function delete(Request $request, BooksReservations $booksReservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booksReservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($booksReservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('books_reservations_index', [], Response::HTTP_SEE_OTHER);
    }
}
