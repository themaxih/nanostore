<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Service\Snackbar\AbstractSnackbar;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseSecurityController extends AbstractController
{
    protected Utilisateur $user;
    protected bool $userConnected = false;

    protected function addSnackbar(AbstractSnackbar $snackbar): void
    {
        try {
            $this->addFlash("snackbars", $snackbar->htmlCode());
        } catch (LogicException) {
            /*
                Ces erreurs ne sont jamais censé arriver
                addFlash dépend de la pile de requêtes 'request_stack'
                qui devrait être toujours disponible et la session est
                généralement activée par défaut et gérée automatiquement par Symfony
            */
        }
    }

    public function initializeUser(): void
    {
        $user = parent::getUser();
        if ($user instanceof Utilisateur) {
            $this->user = $user;
            $this->userConnected = true;
        }
    }
}