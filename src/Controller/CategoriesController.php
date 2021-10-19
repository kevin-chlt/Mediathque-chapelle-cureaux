<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoriesController extends AbstractController
{
    // Check the form & create new category entity
    #[Route('/new', name: 'categories_new', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE',  message: 'Vous n\'êtes pas autorisé à effectué cette action')]
    public function new(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie crée avec succès');
        } else {
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                $this->addFlash('errors', $error->getMessage());
            }
        }

        return $this->redirectToRoute('books_new', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/remove', name: 'categories_delete', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE',  message: 'Vous n\'êtes pas autorisé à effectué cette action')]
    public function delete(Request $request, CategoriesRepository $categoriesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$this->getUser()->getId(), $request->request->get('_token'))) {
            $category = $categoriesRepository->find((int) $request->get('category'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression de la catégorie effectué avec succès');
        }

        return $this->redirectToRoute('books_new', [], Response::HTTP_SEE_OTHER);
    }
}
