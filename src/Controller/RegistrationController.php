<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Service\Snackbar\ErrorSnackbar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;

class RegistrationController extends BaseSecurityController {
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        AuthenticatorInterface $authenticator
    ): Response {
        if ($this->userConnected) {
            return $this->redirectToRoute('user_page');
        }
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'translation_domain' => false,
            'attr' => ['id' => 'register'],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                /** Correction du prénom et du nom @see cleanName */
                $firstName = $user->getFirstName();
                if (!is_null($redirectResponse = $this->cleanName($firstName))) {
                    return $redirectResponse;
                }
                $user->setFirstName($firstName);

                $lastName = $user->getLastName();
                if (!is_null($redirectResponse = $this->cleanName($lastName))) {
                    return $redirectResponse;
                }
                $user->setLastName($lastName);

                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request,
                    [
                        new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                        new RememberMeBadge(),
                    ]
                );
            }

            foreach ($this->getErrorsMessage($form) as $errorMessage) {
                $this->addSnackbar(new ErrorSnackbar("Une erreur c'est produite !", $errorMessage));
            }
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function getErrorsMessage(FormInterface $form): array {
        return array_map(
            fn($error) => $error->getMessage(),
            iterator_to_array($form->getErrors(true))
        );
    }

    /**
     * Cette fonction a pour but de nettoyer le nom ou le prénom donné
     * par l'utilisateur. Elle supprime les caractères indésirables et
     * met en forme la chaîne, notamment en mettant la première lettre
     * en majuscule et le reste en minuscule.
     * Les prénoms composés sont également pris en charge.
     * La fonction renvoie une éventuelle réponse de redirection si
     * quelque chose se passe mal (par exemple, une partie d'un nom composé
     * de longueur 1).
     *
     * @param string $name Le nom ou le prénom passé en paramètre (passé par référence).
     * @return RedirectResponse|null Une éventuelle réponse en cas d'erreur.
     */
    private function cleanName(string &$name): ?RedirectResponse {
        // Enlève les tirets du début et de la fin de la chaine
        $name = trim($name, "-\n\r\t\v\0");

        // Supprime les espaces
        $name = str_replace(' ', '', $name);

        // Remplace les multiples tirets par un seul
        $name = preg_replace('/-+/', '-', $name);

        // Divise en sous mots pour les prénoms composés
        $multipleName = explode('-', $name);

        // Si le prénom est composé
        if (count($multipleName) > 0) {
            // On parcourt chaque sous mot
            foreach ($multipleName as $key => $part) {
                // Cas ou le sous mot est de longueur 1, ce qui est invalide.
                if (strlen($part) == 1) {
                    $this->addSnackbar(new ErrorSnackbar(
                        'Une erreur est survenu.',
                        "Votre nom ou prénom n'est pas valide. Merci de bien vouloir vérifier vos informations."
                    ));
                    return $this->redirectToRoute('register');
                }
                $multipleName[$key] = ucfirst(strtolower($part));
            }
            $name = implode('-', $multipleName);
        } else { // Si le prénom n'est pas composé
            $name = ucfirst(strtolower($name));
        }

        return null;
    }
}
