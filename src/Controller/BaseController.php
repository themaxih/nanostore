<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\SousCategorieRepository;
use App\Service\Snackbar\ErrorSnackbar;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class BaseController extends BaseSecurityController
{
    protected array $categories = [];
    protected array $subCategories = [];
    protected int $nombreArticle = 0;

    protected function saveTargetPath(SessionInterface $session, string $route, array $parameters = []): void
    {
        $session->set(
            '_security.main.target_path',
            $this->generateUrl(
                $route,
                $parameters,
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
    }

    public function initializeData(
        CategorieRepository $categorieRepository,
        SousCategorieRepository $sousCategorieRepository
    ): void
    {
        if ($this->userConnected) {
            $this->nombreArticle = $this->user->countCartProducts();
        }

        foreach ($categorieRepository->findAll() as $categorie) {
            $this->categories[$categorie->getHrefName()] = $categorie;
            foreach ($sousCategorieRepository->findBy(['category' => $categorie->getId()]) as $sousCategorie) {
                $this->subCategories[$categorie->getHrefName()][$sousCategorie->getHrefName()] = $sousCategorie;
            }
        }
    }

    protected function addErrorsAndRedirect(
        ConstraintViolationListInterface $violations,
        string $route
    ): RedirectResponse
    {
        foreach ($violations as $violation) {
            $this->addSnackbar(new ErrorSnackbar(
                "Une erreur s'est produite.",
                $violation->getMessage()
            ));
        }
        return $this->redirectToRoute($route);
    }
}