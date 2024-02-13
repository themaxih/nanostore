<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends BaseController
{
    #[Route('/forgot-password', name: 'forgot_password')] // TODO
    public function forgotPassword(): Response {
        return $this->render('home.html.twig', [
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }
}
