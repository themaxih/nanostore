<?php

namespace App\EventListener;

use App\Controller\BaseController;
use App\Controller\BaseSecurityController;
use App\Repository\CategorieRepository;
use App\Repository\SousCategorieRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;


class ControllerListener
{
    public function __construct(
        private readonly CategorieRepository $categorieRepository,
        private readonly SousCategorieRepository $sousCategorieRepository
    ) {}

    #[AsEventListener]
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            if ($controller[0] instanceof BaseSecurityController) {
                $controller[0]->initializeUser();
            }

            if ($controller[0] instanceof BaseController) {
                $controller[0]->initializeData($this->categorieRepository, $this->sousCategorieRepository);
            }
        }
    }
}