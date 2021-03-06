<?php

namespace App\Controller;


use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/users')]
class UsersController extends AbstractController
{
    // Return user no validate by employee
    #[Route('/admin', name: 'users_index')]
    #[IsGranted('ROLE_EMPLOYEE', message: 'Vous n\'êtes pas autorisé à accéder à cette page')]
    public function panelAdmin (UsersRepository $usersRepository) : Response
    {
        return $this->render('users/admin-index.html.twig', [
            'users' => $usersRepository->findBy(['isValidate' => false])
        ]);
    }

    // Delete the user when employee refuse the registration
    #[Route('remove/{id}', name: 'user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEE', message: 'Vous n\'êtes pas autorisé à effectué cette action')]
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
    #[IsGranted('ROLE_EMPLOYEE', message: 'Vous n\'êtes pas autorisé à effectué cette action')]
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

    // Get user details and form for update user details.
    #[Route('/', name: 'user_update', methods: ['GET', 'POST'])]
    public function userUpdate (Request $request, UsersRepository $usersRepository, UserPasswordHasherInterface $hasher) : Response
    {
        $user = $usersRepository->find($this->getUser());
        $form = $this->createForm(RegistrationFormType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$form->get('plainPassword')->isEmpty()){
                $user->setPassword($hasher->hashPassword($user, $form->get('plainPassword')->getData()));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil à été mise à jour avec succès.');
        }

        return $this->render('users/user-index.html.twig',[
            'form' => $form->createView(),
        ]);
    }

}