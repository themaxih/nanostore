<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Repository\AdresseRepository;
use App\Repository\CommandeRepository;
use App\Service\Snackbar\ErrorSnackbar;
use App\Service\Snackbar\SuccessSnackbar;
use App\Service\Validation\AddressValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserPageController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdresseRepository $adresseRepository,
        private readonly AddressValidatorService $addressValidator
    ) {}

    #[Route('/profile', name: 'user_page')]
    public function profile(): Response {
        return $this->redirectToRoute('user_informations');
    }

    #[Route('/profile/informations', name: 'user_informations')]
    public function profileInformations(Request $request): Response {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        $session = $request->getSession();
        $snackbarType = $session->get("snackbarType");
        if (!is_null($snackbarType)) {
            $session->remove("snackbarType");
            switch ($snackbarType) {
                case "success":
                    $this->addSnackbar(new SuccessSnackbar(
                        "Confirmation",
                        "Votre mot de passe à bien été modifié."
                    ));
                    break;
                case "errorIncorrectPwd":
                    $this->addSnackbar(new ErrorSnackbar(
                        "Impossible de changer le mot de passe",
                        "Le mot de passe actuel est incorrect, veuillez réessayer."
                    ));
                    break;
                case "errorSamePwd":
                    $this->addSnackbar(new ErrorSnackbar(
                        "Impossible de changer le mot de passe",
                        "Le nouveau mot de passe est identique à l'ancien, veuillez choisir un autre mot de passe."
                    ));
                    break;
            }
        }

        return $this->render('user_page/user_informations.html.twig', [
            'identifier' => $this->user->getUserIdentifier(),
            'email' => $this->user->getEmail(),
            'firstName' => $this->user->getFirstName(),
            'lastName' => $this->user->getLastName(),
            'gender' => $this->user->getGender(),
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/profile/addresses', name: 'user_addresses')]
    public function profileAddresses(): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        $addresses = $this->adresseRepository->findBy([
            'user' => $this->user->getUserId()
        ]);
        $defaultAddress = $this->user->getDefaultAddress();

        return $this->render('user_page/addresses/user_addresses.html.twig', [
            'identifier' => $this->user->getUserIdentifier(),
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/profile/addresses/addAddress', name: 'addAddress')]
    public function addAddress(Request $request): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        if ($request->getMethod() == 'GET') {
            return $this->render('user_page/addresses/add_address.html.twig', [
                'identifier' => $this->user->getUserIdentifier(),
                'nombreArticle' => $this->nombreArticle,
                'categories' => $this->categories,
                'sousCategories' => $this->subCategories,
            ]);
        }


        $gender = $request->request->getString('gender');
        $firstName = $request->request->getString('firstName');
        $phoneNumber = $request->request->getString('phoneNumber');
        $lastName = $request->request->getString('lastName');
        $postalCode = $request->request->getString('postalCode');
        $city = $request->request->getString('city');
        $addressLine = $request->request->getString('address');

        $violations = $this->addressValidator->validateFields($gender,
            $firstName, $lastName, $phoneNumber,
            $addressLine, $postalCode, $city
        );
        if ($violations->count() > 0) {
            return $this->addErrorsAndRedirect($violations, 'addAddress');
        }

        $gender = (bool) $gender;

        $address = $this->adresseRepository->findOneBy([
            'user' => $this->user->getUserId(), 'gender' => $gender,
            'firstName' => $firstName, 'lastName' => $lastName,
            'phoneNumber' => $phoneNumber, 'addressLine' => $addressLine,
            'postalCode' => $postalCode, 'city' => $city
        ]);

        if (!is_null($address)) {
            $this->addSnackbar(new ErrorSnackbar(
                "Une erreur s'est produite.",
                "L'adresse existe déjà. Si vous souhaitez la modifier, allez dans la section \"Mes adresses\"."
            ));
            return $this->redirectToRoute('addAddress');
        }

        $newAddress = new Adresse(
            $this->user, $gender,
            $firstName, $lastName,
            $phoneNumber, $addressLine,
            $postalCode, $city
        );

        $this->entityManager->persist($newAddress);

        $isDefault = $request->request->getBoolean('is-default');

        if ($isDefault) {
            $this->user->setDefaultAddress($newAddress);
        }

        $this->entityManager->flush();

        $this->addSnackbar(new SuccessSnackbar(
            "Opération réussis",
            "L'adresse a été ajouté avec succès."
        ));
        return $this->redirectToRoute('user_addresses');
    }

    #[Route('/profile/addresses/editAddress', name: 'editAddress', methods: 'POST')]
    public function editAddress(Request $request): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        $addressId = $request->request->getInt('addressId');
        if ($addressId === 0) {
            $this->addSnackbar(new ErrorSnackbar(
                "Une erreur s'est produite.",
                "L'adresse demandée n'existe pas."
            ));
            return $this->redirectToRoute('user_addresses');
        }

        $address = $this->adresseRepository->findOneBy([
            'id' => $addressId,
            'user' => $this->user->getUserId()
        ]);

        if (is_null($address)) {
            $this->addSnackbar(new ErrorSnackbar(
                "Une erreur s'est produite.",
                "Impossible de modifier l'adresse, veuillez réessayer."
            ));
            return $this->redirectToRoute('user_addresses');
        }

        return $this->render('user_page/addresses/edit_address.html.twig', [
            'identifier' => $this->user->getUserIdentifier(),
            'adresse' => $address,
            'nombreArticle' => $this->nombreArticle,
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/profile/addresses/modifyAddress', name: 'modifyAddress', methods: 'POST')]
    public function modifyAddress(Request $request): Response {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        $gender = $request->request->getString('gender');
        $city = $request->request->getString('city');
        $postalCode = $request->request->getString('postalCode');
        $addressLine = $request->request->getString('address');
        $phoneNumber = $request->request->getString('phoneNumber');
        $lastName = $request->request->getString('lastName');
        $firstName = $request->request->getString('firstName');

        $violations = $this->addressValidator->validateFields($gender,
            $firstName, $lastName, $phoneNumber,
            $addressLine, $postalCode, $city
        );

        if ($violations->count() > 0) {
            return $this->addErrorsAndRedirect($violations, 'editAddress');
        }

        $gender = (bool) $gender;

        $addressId = $request->request->getInt('addressId');
        if ($addressId === 0) {
            $this->addSnackbar(new ErrorSnackbar(
                "Une erreur s'est produite.",
                "La modification de l'adresse a échoué, merci de bien vouloir réessayer."
            ));
            return $this->redirectToRoute('user_addresses');
        }

        $address = $this->adresseRepository->findOneBy([
            'id' => $addressId,
            'user' => $this->user->getUserId()
        ]);

        if (is_null($address)) {
            $this->addSnackbar(new ErrorSnackbar(
                "Une erreur s'est produite.",
                "Impossible de modifier l'adresse, veuillez réessayer."
            ));
            return $this->redirectToRoute('user_addresses');
        }

        $address->setGender($gender);
        $address->setFirstName($firstName);
        $address->setLastName($lastName);
        $address->setPhoneNumber($phoneNumber);
        $address->setAddressLine($addressLine);
        $address->setPostalCode($postalCode);
        $address->setCity($city);

        $this->entityManager->flush();

        $this->addSnackbar(new SuccessSnackbar(
            "Opération réussis",
            "L'adresse a bien été modifiée."
        ));

        return $this->redirectToRoute('user_addresses');
    }

    #[Route('/profile/addresses/removeAddress', name: 'removeAddress', methods: 'POST')]
    public function removeAddress(Request $request): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        $addressId = $request->request->getInt('addressId');
        if ($addressId === 0) {
            $this->addSnackbar(new ErrorSnackbar(
                "Une erreur s'est produite.",
                "Impossible de supprimer l'adresse, veuillez réessayer."
            ));
            return $this->redirectToRoute('user_addresses');
        }

        $address = $this->adresseRepository->findOneBy([
            'id' => $addressId,
            'user' => $this->user->getUserId()
        ]);

        if (is_null($address)) {
            $this->addSnackbar(new ErrorSnackbar(
                "Une erreur s'est produite.",
                "Impossible de supprimer l'adresse, veuillez réessayer."
            ));
            return $this->redirectToRoute('user_addresses');
        }

        $defaultAddress = $this->user->getDefaultAddress();
        if (!is_null($defaultAddress) && $defaultAddress->getId() === $addressId) {
            $this->user->setDefaultAddress(null);
        }

        $this->entityManager->remove($address);
        $this->entityManager->flush();
        $this->addSnackbar(new SuccessSnackbar(
            'Opération réussis',
            "L'adresse a été supprimée avec succès."
        ));

        return $this->redirectToRoute('user_addresses');
    }

    #[Route('/profile/addresses/changeDefaultAddress', name: 'changeDefaultAddress', methods: 'POST')]
    public function changeDefaultAddress(Request $request): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        $addressId = $request->request->getInt('selectedAddress');

        if ($addressId === 0) {
            return new JsonResponse("Changement non effectué, l'adresse n'existe pas.");
        }

        $defaultAddress = $this->user->getDefaultAddress();

        if (!is_null($defaultAddress) && $addressId === $defaultAddress->getId()) {
            $this->user->setDefaultAddress(null);
        } else {
            $address = $this->adresseRepository->findOneBy([
                'id' => $addressId,
                'user' => $this->user->getUserId()
            ]);

            if (is_null($address)) {
                return new JsonResponse("Changement non effectué, l'adresse n'existe pas.");
            }

            $this->user->setDefaultAddress($address);
        }

        $this->entityManager->flush();
        return new JsonResponse('Changement effectué.');
    }

    #[Route('/profile/orders', name: 'user_orders')]
    public function profileOrders(CommandeRepository $commandeRepository): Response {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        return $this->render('user_page/user_orders.html.twig', [
            'identifier' => $this->user->getUserIdentifier(),
            'nombreArticle' => $this->nombreArticle,
            'commandes' => $commandeRepository->findOrdersByUserId($this->user->getUserId()),
            'categories' => $this->categories,
            'sousCategories' => $this->subCategories,
        ]);
    }

    #[Route('/profile/change-password', name: 'password_change', methods: "POST")]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        if (!$this->userConnected) {
            return $this->redirectToRoute('login');
        }

        $oldPassword = $request->request->getString('old-password');
        $newPassword = $request->request->getString('new-password');
        $session = $request->getSession();

        // Vérifiez si l'ancien mot de passe est correct
        if (!$passwordHasher->isPasswordValid($this->user, $oldPassword)) {
            $session->set("snackbarType", "errorIncorrectPwd");
            return $this->redirectToRoute('user_informations');
        }

        if ($oldPassword === $newPassword) {
            $session->set("snackbarType", "errorSamePwd");
            return $this->redirectToRoute('user_informations');
        }

        // Si l'ancien mot de passe est correct, encodez et définissez le nouveau mot de passe
        $encodedNewPassword = $passwordHasher->hashPassword($this->user, $newPassword);
        $this->user->setPassword($encodedNewPassword);
        $this->entityManager->flush();

        $session->set("snackbarType", "success");

        return $this->redirectToRoute('user_informations');
    }
}
