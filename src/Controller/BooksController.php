<?php

namespace App\Controller;

use App\Entity\Books;
use App\Form\BooksType;
use App\Repository\BooksRepository;
use App\Services\ImgUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/books")
 */
#[Route('/books')]
class BooksController extends AbstractController
{

    /**
     * @Route("/", name="books_index")
     */
    #[Route('/', name: 'books_index', methods: ['GET'])]
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
    #[Route('/new', name: 'books_new', methods: ['GET','POST'])]
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

            return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('books/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="books_show")
     */
    #[Route('/{id}', name: 'books_show', methods: ['GET'])]
    public function show(Books $book): Response
    {
        return $this->render('books/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="books_edit")
     */
    #[Route('/{id}/edit', name: 'books_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Books $book): Response
    {
        $form = $this->createForm(BooksType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('books/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'books_delete', methods: ['POST'])]
    public function delete(Request $request, Books $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
    }
}
