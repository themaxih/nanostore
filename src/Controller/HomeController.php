<?php

namespace App\Controller;

use App\Entity\UtilisateurNewsletter;
use App\Repository\UtilisateurNewsletterRepository;
use App\Service\Snackbar\ErrorSnackbar;
use App\Service\Snackbar\InformationsSnackbar;
use App\Service\Snackbar\WarningSnackbar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends BaseController
{
    #[Route('/', name: 'home')]
    public function home(Request $request): Response {
        $response = new Response();
        $nouvelUtilisateur = !$request->cookies->has('dejaVisite');

        if ($nouvelUtilisateur) {
            // Créez un cookie pour marquer cette visite
            $cookie = new Cookie('dejaVisite', '1', time() + 2_629_800); // Expiration dans 1 mois
            $response->headers->setCookie($cookie);

            // Ajoutez la logique pour afficher la snackbar aux nouveaux utilisateurs
            $this->addSnackbar(new WarningSnackbar(
                "Bienvenue sur NanoStore !",
                "Découvrez nos dernières collections et profitez de nos offres exclusives.
                        Si vous avez des questions, notre service client est là pour vous aider."
            ));
        }
        return $this->render('home.html.twig', [
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ], $response);
    }

    const ERROR_INVALID_EMAIL = "L'adresse mail indiquée est invalide, merci de réessayer avec une adresse mail valide.";
    const ERROR_ALREADY_SUBSCRIBED = "Il semble que vous soyez déjà abonné à notre newsletter.";

    #[Route('/subscribe-newsletter', name: 'subscribe-newsletter', methods: "POST")]
    public function subscribeNewsletter(
        Request $request,
        UtilisateurNewsletterRepository $unRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response {

        $email = $request->request->getString('email');
        $baseUrl = $request->request->getString('current-url');

        $errors = $validator->validate($email, [
            new Email(),
            new NotBlank(['message' => self::ERROR_INVALID_EMAIL])
        ]);

        if ($errors->count() > 0)
            $this->addSnackbar(new ErrorSnackbar('Une erreur est survenue', $errors[0]->getMessage()));
        else if ($unRepository->find($email) !== null)
            $this->addSnackbar(new WarningSnackbar('Abonnement Newsletter', self::ERROR_ALREADY_SUBSCRIBED));
        else {
            $entityManager->persist(new UtilisateurNewsletter($email));
            $entityManager->flush();
            $this->addSnackbar(new InformationsSnackbar(
                "Abonnement à la newsletter",
                "Votre abonnement à la newsletter à bien été pris en compte."
            ));
        }

        return $this->redirect($baseUrl);
    }
}
