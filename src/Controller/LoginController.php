<?php

namespace App\Controller;

use App\Service\Snackbar\ErrorSnackbar;
use App\Service\Snackbar\SuccessSnackbar;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends BaseSecurityController implements EventSubscriberInterface
{
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator): Response {
        if ($this->userConnected) {
            return $this->redirectToRoute('user_page');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if (!is_null($error)) {
            $translatedErrorMessage = $translator->trans($error->getMessage(), domain: 'login');
            $this->addSnackbar(new ErrorSnackbar("Une erreur s'est produite !", $translatedErrorMessage));
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
        ]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void {
        throw new LogicException('Logout error.');
    }

    public function onLogout(): void
    {
        $this->addSnackbar(new SuccessSnackbar("Opération réussis", "Vous avez été déconnecté avec succès."));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}