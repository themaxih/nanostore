<?php

namespace App\Service\Validation;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PaymentValidatorService extends AbstractValidatorService
{
    /**
     * Cette méthode à pour but de valider des données récupérées
     * correspondant à un paiement. Les paramètres de la méthode
     * sont passé par référence, ce qui veut dire qu'ils seront modifié.
     * @param string $cardNumbers
     * @param string $expirationDate
     * @param string $cardName
     * @param string $csc
     * @return ConstraintViolationList
     */
    public function validateFields(
        string &$cardNumbers, string $expirationDate,
        string &$cardName, string &$csc
    ): ConstraintViolationList
    {
        // Vérification des numéros de la carte de crédit

        $cardNumbers = str_replace(' ', '', $cardNumbers);
        $violations = $this->validator->validate($cardNumbers, [
            new NotBlank(message: 'Vous devez renseigner votre numéro de carte de crédit.'),
            new Length(16, exactMessage: 'Vous devez renseigner les 16 chiffres de votre carte de crédit.'),
            new Type('digit', 'Le champ des chiffres de votre carte de crédit ne doit contenir que des chiffres.'),
            new Callback(function (string $cardNumbers, ExecutionContextInterface $context) {
                if (!$this->isLuhnValid($cardNumbers)) {
                    $context->buildViolation("Le numéro de carte de crédit n'est pas valide.")
                        ->addViolation();
                }
            })
        ]);

        if (count($violations) > 0) {
            return $violations;
        }

        // Vérification de la date d'expiration de la carte de crédit
        $now = new \DateTime();
        $violations = $this->validator->validate($expirationDate, [
            new NotBlank(message: "La date d'expiration de votre carte de crédit ne peut pas être vide."),
            new DateTime('Y-m-d', "La date d'expiration de votre carte de crédit n'est pas au bon format."),
            new GreaterThanOrEqual(
                "{$now->format('Y-m-01')}",
                message: "La date d'expiration doit être au moins égale au mois d'aujourd'hui."
            ),
            new LessThanOrEqual(
                "{$now->modify('+4 years')->format('Y-12-01')}",
                message: "La date d'expiration ne doit pas excéder quatre ans dans le futur."
            )
        ]);

        if (count($violations) > 0) {
            return $violations;
        }


        // Vérification du nom de la carte de crédit
        $cardName = trim($cardName, "- \n\r\t\v\0");
        $cardName = preg_replace('/\s+/', ' ', $cardName);
        $cardName = preg_replace('/-+/', '-', $cardName);
        $violations = $this->validator->validate($cardName, [
            new NotBlank(message: 'Le nom de la carte ne peut pas être vide.'),
            new Callback(function (string $cardName, ExecutionContextInterface $context) {
                if (!preg_match('/^[\p{L}\s-]+$/u', $cardName)) {
                    $context->buildViolation('Le nom de la carte ne doit contenir que des lettres et/ou des tirets.')
                        ->addViolation();
                }
            })
        ]);

        if (count($violations) > 0) {
            return $violations;
        }


        // Vérification du CVC
        $csc = trim($csc);
        return $this->validator->validate($csc, [
            new NotBlank(message: 'Le CVC ne peut pas être vide.'),
            new Type('digit', 'Le CVC ne doit contenir que des chiffres.'),
            new Length(3, exactMessage: 'Le CVC doit être composé de 3 chiffres.')
        ]);
    }

    /**
     * Valide une chaîne numérique en utilisant l'algorithme de Luhn.
     *
     * L'algorithme de Luhn est utilisé pour vérifier la validité de numéros de séquence,
     * typiquement des numéros de cartes de crédit. Cette méthode implémente l'algorithme
     * pour vérifier si un numéro donné est valide selon cette méthode de calcul.
     *
     * @param string $number La chaîne numérique à valider, généralement un numéro de carte de crédit.
     *
     * @return bool Renvoie true si le numéro est valide selon l'algorithme de Luhn, false sinon.
     *
     * @note Cette fonction vérifie seulement la validité structurelle du numéro et non son authenticité.
     *
     * @see https://fr.wikipedia.org/wiki/Formule_de_Luhn Pour plus d'informations sur l'algorithme de Luhn.
     */
    private function isLuhnValid(string $number): bool
    {
        if (strlen($number) !== 16 || !ctype_digit($number)) {
            return false;
        }

        $sum = 0;
        $shouldDouble = false;
        // Commence par le dernier chiffre et travaille en arrière
        foreach (str_split(strrev($number)) as $char) {
            $digit = (int) $char;
            if ($shouldDouble) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
            $shouldDouble = !$shouldDouble;
        }
        return $sum % 10 === 0;
    }
}