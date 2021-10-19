<?php

namespace App\Controller;

use App\Entity\Authors;
use App\Form\AuthorsType;
use App\Repository\AuthorsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/authors')]
class AuthorsController extends AbstractController
{
    // Check the form & create new author entity
    #[Route('/new', name: 'authors_new', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE',  message: 'Vous n\'êtes pas autorisé à effectué cette action')]
    public function new(Request $request): Response
    {
        $author = new Authors();
        $form = $this->createForm(AuthorsType::class, $author);
        $form->handleRequest($request);

        if($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($author);
            $entityManager->flush();

            $this->addFlash('success', 'Auteur crée avec succès');
        } else {
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                $this->addFlash('errors', $error->getMessage());
            }
        }

        return $this->redirectToRoute('books_new', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/remove', name: 'authors_delete', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE',  message: 'Vous n\'êtes pas autorisé à effectué cette action')]
    public function delete(Request $request, AuthorsRepository $authorsRepository): Response
    {
        if((int)$request->get('author') === 0) {
            return $this->redirectToRoute('books_new', [], Response::HTTP_SEE_OTHER);
        }

        $author = $authorsRepository->find((int) $request->get('author'));

        if (!$author->getBooks()->isEmpty()) {
            $this->addFlash('errors', 'Impossible de supprimer l\'auteur, un ou plusieurs livres y sont rattachés');
        }

        if ($this->isCsrfTokenValid('delete'.$this->getUser()->getId(), $request->request->get('_token')) && $author->getBooks()->isEmpty()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($author);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression de l\'auteur effectué avec succès');
        }

        return $this->redirectToRoute('books_new', [], Response::HTTP_SEE_OTHER);
    }
}
