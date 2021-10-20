<?php

namespace App\Security;

use App\Repository\UsersRepository;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $router;
    private $userRepository;
    private Security $security;

    public function __construct(UrlGeneratorInterface $router, UsersRepository $userRepository, Security $security)
    {
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): RedirectResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $this->security->getUser()->getUserIdentifier()]);

        if(!$user->getIsValidate()) {
            $request->getSession()->getFlashBag()->add('errors', 'Vous n\'avez pas encore été accepté par un administrateur.');
            return new RedirectResponse($this->router->generate('app_login'));
        } else {
            $request->getSession()->getFlashBag()->add('errors', $accessDeniedException->getMessage());
            return new RedirectResponse($this->router->generate('books_index'));
        }
    }
}