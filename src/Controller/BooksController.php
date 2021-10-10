<?php

namespace App\Controller;

use App\Entity\Books;
use App\Form\BooksType;
use App\Repository\BooksRepository;
use App\Repository\BooksReservationsRepository;
use App\Services\ImgUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/books")
 */
class BooksController extends AbstractController
{

    /**
     * @Route("/", name="books_index")
     */
    public function index(BooksRepository $booksRepository): Response
    {
        return $this->render('books/index.html.twig', [
            'books' => $booksRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="books_new")
     */
    #Add new book method
    public function new(Request $request, ImgUploader $uploader): Response
    {
        $book = new Books();
        $form = $this->createForm(BooksType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Give new name if an image has been uploaded //
            $file = $form['cover']->getData();
            if($file instanceof UploadedFile){
                $filename = $uploader->getFileName($file);
                $book->setCover("uploads/$filename");
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Livre ajouté au catalogue avec succès');
            return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('books/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="books_show")
     */
    public function show(Books $book): Response
    {
        return $this->render('books/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/remove/{id}", name="books_delete", methods={"POST"})
     */
    public function delete(Request $request, Books $book, BooksReservationsRepository $reservationsRepository): Response
    {

        $reservations = $reservationsRepository->findOneBy(['books' => $book->getId()]);
        if($reservations) {
            $this->addFlash('errors', 'Un emprunt est en cours pour ce livre, veuillez le supprimer avant de supprimer ce livre');
        } elseif ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();

            $this->addFlash('success', 'Livre effacé du catalogue avec succès');
        }

        return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
    }
}
