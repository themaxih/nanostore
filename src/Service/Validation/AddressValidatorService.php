<?php

namespace App\Service\Validation;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AddressValidatorService extends AbstractValidatorService
{
    /**
     * Cette méthode à pour but de valider des données récupérées
     * correspondant à une adresse. Les paramètres de la méthode
     * sont passé par référence, ce qui veut dire qu'ils seront modifié.
     * @param string $gender
     * @param string $firstName
     * @param string $lastName
     * @param string $phoneNumber
     * @param string $addressLine
     * @param string $postalCode
     * @param string $city
     * @return ConstraintViolationListInterface
     */
    public function validateFields(string $gender,
        string &$firstName, string &$lastName, string &$phoneNumber,
        string &$addressLine, string &$postalCode, string &$city
    ): ConstraintViolationListInterface
    {
        // Vérification du genre
        $violations = $this->validator->validate($gender, [
            new Choice(choices: ['0', '1'], message: 'Veuillez séléctionner votre genre.')
        ]);

        if (count($violations) > 0) {
            return $violations;
        }


        // Vérification du prénom
        $firstName = trim($firstName, "-\n\r\t\v\0");
        $firstName = str_replace(' ', '', $firstName);
        $firstName = preg_replace('/-+/', '-', $firstName);
        $violations = $this->validator->validate($firstName, [
            new NotBlank(message: "Veuillez renseigner un prénom."),
            new Length([
                'min' => 2,
                'minMessage' => 'Le prénom doit faire au moins {{ limit }} caractères.',
                'max' => 32,
            ]),
            new Callback(function (string $firstName, ExecutionContextInterface $context) {
                if (!preg_match('/^[\p{L}\s-]+$/u', $firstName)) {
                    $context->buildViolation('Le prénom ne doit contenir que des lettres et/ou des tirets.')
                        ->addViolation();
                }
            })
        ]);

        if (count($violations) > 0) {
            return $violations;
        }


        // Vérification du nom
        $lastName = trim($lastName, "-\n\r\t\v\0");
        $lastName = str_replace(' ', '', $lastName);
        $lastName = preg_replace('/-+/', '-', $lastName);
        $violations = $this->validator->validate($lastName, [
            new NotBlank(message: "Veuillez renseigner un nom."),
            new Length([
                'min' => 2,
                'minMessage' => 'Le nom doit faire au moins {{ limit }} caractères.',
                'max' => 32,
            ]),
            new Callback(function (string $lastName, ExecutionContextInterface $context) {
                if (!preg_match('/^[\p{L}\s-]+$/u', $lastName)) {
                    $context->buildViolation('Le nom ne doit contenir que des lettres et/ou des tirets.')
                        ->addViolation();
                }
            })
        ]);

        if (count($violations) > 0) {
            return $violations;
        }


        // Vérification du numéro de téléphone
        $phoneNumber = trim($phoneNumber);
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        $violations = $this->validator->validate($phoneNumber, [
            new NotBlank(message: 'Le numéro de téléphone ne peut pas être vide.'),
            new Type('digit', 'Le numéro de téléphone ne doit contenir que des chiffres.')
        ]);

        if (count($violations) > 0) {
            return $violations;
        }


        // Vérification de l'adresse
        $addressLine = trim($addressLine);
        $violations = $this->validator->validate($addressLine, [
            new NotBlank(message: "L'adresse ne peut pas être vide.")
        ]);

        if (count($violations) > 0) {
            return $violations;
        }


        // Vérification du code postal
        $postalCode = trim($postalCode);
        $violations = $this->validator->validate($postalCode, [
            new NotBlank(message: 'Le code postal ne peut pas être vide.'),
            new Type('digit', 'Le code postal ne doit contenir que des chiffres.'),
            new Length(5, exactMessage: 'Le code postal doit être composé de 5 chiffres')
        ]);

        if (count($violations) > 0) {
            return $violations;
        }


        // Vérification de la ville
        $city = trim($city, "-\n\r\t\v\0");
        $city = str_replace(' ', '', $city);
        $city = preg_replace('/-+/', '-', $city);
        return $this->validator->validate($city, [
            new NotBlank(message: 'La ville ne peut pas être vide.'),
            new Callback(function (string $city, ExecutionContextInterface $context) {
                if (!preg_match('/^[\p{L}\s-]+$/u', $city)) {
                    $context->buildViolation('La ville ne doit contenir que des lettres.')
                        ->addViolation();
                }
            })
        ]);
    }
}