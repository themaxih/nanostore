<?php

namespace App\Service\Snackbar;

abstract class AbstractSnackbar
{
    public function __construct(
        protected string $title,
        protected string $message
    )
    {
    }

    abstract public function htmlCode(): string;
}