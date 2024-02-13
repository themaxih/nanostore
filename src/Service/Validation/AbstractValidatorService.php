<?php

namespace App\Service\Validation;

use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidatorService
{
    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }
}