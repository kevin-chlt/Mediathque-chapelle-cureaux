<?php

namespace App\Controller;

use App\Entity\Authors;
use App\Form\AuthorsType;
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



    #[Route('/{id}', name: 'authors_delete', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE',  message: 'Vous n\'êtes pas autorisé à effectué cette action')]
    public function delete(Request $request, Authors $author): Response
    {
        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($author);
            $entityManager->flush();
        }

        return $this->redirectToRoute('books_new', [], Response::HTTP_SEE_OTHER);
    }
}
