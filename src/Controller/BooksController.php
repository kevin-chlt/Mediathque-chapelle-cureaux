<?php

namespace App\Controller;

use App\Data\FiltersBooks;
use App\Entity\Books;
use App\Form\AuthorsType;
use App\Form\BooksType;
use App\Form\CategoriesType;
use App\Form\FiltersType;
use App\Repository\AuthorsRepository;
use App\Repository\BooksRepository;
use App\Repository\BooksReservationsRepository;
use App\Repository\CategoriesRepository;
use App\Services\ImgUploader;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/books')]
class BooksController extends AbstractController
{
    // Get the catalogue or search filter page
    #[Route('/', name: 'books_index', methods: ['GET', 'POST'])]
    public function index(BooksRepository $booksRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $filterBooks = new FiltersBooks();
        $filterForm = $this->createForm(FiltersType::class, $filterBooks);
        $filterForm->handleRequest($request);

        if($filterForm->isSubmitted() && $filterForm->isValid()) {
           return $this->render('books/index.html.twig', [
                'books' => $booksRepository->getBookByCategory($filterBooks)->getResult(),
                'filterForm' => $filterForm->createView()
            ]);
        }

        return $this->render('books/index.html.twig', [
            'books' => $paginator->paginate($booksRepository->getBooksByIsFree(), $request->query->getInt('page', 1), 3),
            'filterForm' => $filterForm->createView()
        ]);
    }


    // Add new book method & render form add Author & Categories
    #[Route('/new', name: 'books_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EMPLOYEE', message: 'Vous n\'êtes pas autorisé à accéder à cette page')]
    public function new(Request $request, ImgUploader $uploader, CategoriesRepository $categoriesRepository, AuthorsRepository $authorsRepository): Response
    {
        $book = new Books();
        $form = $this->createForm(BooksType::class, $book);
        $newAuthorForm = $this->createForm(AuthorsType::class, null ,  [
            'action' => $this->generateUrl('authors_new')
        ]);
        $newCategoryForm = $this->createForm(CategoriesType::class, null, [
            'action' => $this->generateUrl('categories_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Give new name if an image has been uploaded //
            $file = $form['cover']->getData();
            if($file instanceof UploadedFile){
                $filename = $uploader->getFileName($file);
                $book->setCover("uploads/$filename");
            } else {
                $this->addFlash('errors', 'Une erreur est apparu lors du chargement de votre image, veuillez réessayer.');
                return $this->redirectToRoute('books_index');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Livre ajouté au catalogue avec succès');
            return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('books/new.html.twig', [
            'authors' => $authorsRepository->findAll(),
            'categories' => $categoriesRepository->findAll(),
            'form' => $form->createView(),
            'authorForm' => $newAuthorForm->createView(),
            'categoryForm' => $newCategoryForm->createView()
        ]);
    }

    #[Route('/{id}', name: 'books_show', methods: ['GET'])]
    public function show(Books $book): Response
    {
        return $this->render('books/show.html.twig', [
            'book' => $book,
        ]);
    }


    #[Route('/remove/{id}', name: 'books_delete', methods: ['POST'])]
    public function delete(Request $request, Books $book, BooksReservationsRepository $reservationsRepository): Response
    {
        $reservations = $reservationsRepository->findOneBy(['books' => $book->getId()]);

        if($reservations) {
            $this->addFlash('errors', 'Un emprunt est en cours pour ce livre');
        } elseif ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();

            $this->addFlash('success', 'Livre effacé du catalogue avec succès');
        }

        return $this->redirectToRoute('books_index', [], Response::HTTP_SEE_OTHER);
    }
}
