<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\PanierProduit;
use App\Repository\AdresseRepository;
use App\Repository\PanierProduitRepository;
use App\Service\OrderBuilder;
use App\Service\Snackbar\SuccessSnackbar;
use App\Service\Snackbar\WarningSnackbar;
use App\Service\Validation\AddressValidatorService;
use App\Service\Validation\PaymentValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends BaseController
{
    #[Route('/order', name: 'order')]
    public function order(
        Request $request,
        PanierProduitRepository $ppRepository
    ): Response
    {
        if (!$this->userConnected) {
            $this->saveTargetPath($request->getSession(), 'order');
            return $this->redirectToRoute('login');
        }

        $panierProduits = $ppRepository->findCartProductsByUserId($this->user->getUserId());
        $panierProduits = array_filter(
            $panierProduits,
            fn(PanierProduit $cartProduct) => $cartProduct->getProduct()->getQuantityLeft() > 0
        );

        $nombreArticleAchete = count($panierProduits);

        if ($nombreArticleAchete == 0) {
            $this->addSnackbar(new WarningSnackbar(
                "Une erreur c'est produite",
                'Votre panier ne contient aucun article ou des articles en rupture de stock, veuillez réessayez'));
            return $this->redirectToRoute('cart');
        }

        return $this->render('order.html.twig', [
            'panierProduits' => $panierProduits,
            'nombreArticle' => $this->nombreArticle,
            'nombreArticleAchete' => $nombreArticleAchete,
            'adresse' => $this->user->getDefaultAddress(),
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/order/validate', name: 'order_validate', methods: "POST")]
    public function validateOrder(
        Request $request,
        OrderBuilder $orderBuilder,
        AddressValidatorService $addressValidator,
        PaymentValidatorService $paymentValidator,
        EntityManagerInterface $entityManager,
        AdresseRepository $adresseRepository
    ): Response {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }


        $gender = $request->request->getString('gender');
        $firstName = $request->request->getString('firstName');
        $lastName = $request->request->getString('lastName');
        $phoneNumber = $request->request->getString('phoneNumber');
        $addressLine = $request->request->getString('address');
        $postalCode = $request->request->getString('postalCode');
        $city = $request->request->getString('city');

        $violations = $addressValidator->validateFields($gender,
            $firstName, $lastName, $phoneNumber,
            $addressLine, $postalCode, $city
        );
        if ($violations->count() > 0) {
            return $this->addErrorsAndRedirect($violations, 'order');
        }

        $gender = (bool) $gender;

        $cardNumbers = $request->request->getString('card-numbers');
        $expirationDate = $request->request->getString('expiration-date');
        $cardName = $request->request->getString('card-name');
        $csc = $request->request->getString('csc');

        $violations = $paymentValidator->validateFields(
            $cardNumbers, $expirationDate,
            $cardName, $csc
        );
        if ($violations->count() > 0) {
            return $this->addErrorsAndRedirect($violations, 'order');
        }


        // Simulation du paiement validé
        $paiementValide = true;

        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if (!$paiementValide) {
            // Paiement non validé
            return $this->redirectToRoute('order');
        }

        $rememberAddress = $request->request->getBoolean('remember-address');
        if ($rememberAddress) {
            $address = $adresseRepository->findOneBy([
                'user' => $this->user->getUserId(), 'gender' => $gender,
                'firstName' => $firstName, 'lastName' => $lastName,
                'phoneNumber' => $phoneNumber, 'addressLine' => $addressLine,
                'postalCode' => $postalCode, 'city' => $city
            ]);

            if (!is_null($address)) {
                $this->addSnackbar(new WarningSnackbar(
                    "Adresse déjà enregistrée",
                    "L'adresse que vous avez indiquée est déjà enregistrée dans votre liste d'adresse."
                ));
            } else {
                $newAddress = new Adresse(
                    $this->user, $gender,
                    $firstName, $lastName,
                    $phoneNumber, $addressLine,
                    $postalCode, $city
                );
                $entityManager->persist($newAddress);
                $this->addSnackbar(new SuccessSnackbar(
                    "Nouvelle adresse ajoutée",
                    "L'adresse que vous avez renseignée a été ajoutée avec succès."
                ));
            }
        }


        $orderBuilder
            ->createOrderFor($this->user)
            ->withShippingDetails(
                $gender,
                $firstName,
                $lastName,
                $phoneNumber,
                $addressLine,
                $postalCode,
                $city
            )
            ->validateOrder();

        $this->addSnackbar(new SuccessSnackbar(
            "Confirmation de commande",
            "Votre commande à bien été enregistrée.
            Vous allez recevoir un mail de confirmation.
            Retrouvez vos commandes dans la section Mes Commandes de votre profil."
        ));

        return $this->redirectToRoute('home');
    }
}
