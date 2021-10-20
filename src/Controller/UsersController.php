<?php

namespace App\Controller;


use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users')]
class UsersController extends AbstractController
{
    // Return user no validate by employee
    #[Route('/', name: 'users_index')]
    public function index (UsersRepository $usersRepository) : Response
    {
        return $this->render('users/index.html.twig', [
            'users' => $usersRepository->findBy(['isValidate' => false])
        ]);
    }

    // Delete the user when employee refuse the registration
    #[Route('remove/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, Users $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur refusé avec succès.');
        }

        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }

    //Accept user and change validate bool at true
    #[Route('/accept/{id}', name: 'user_accept', methods: ['POST'])]
    public function accept (Request $request, Users $user) : Response
    {
        if ($this->isCsrfTokenValid('accept'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsValidate(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Action bien prise en compte.');
        }
            return $this->redirectToRoute('users_index');
    }

}