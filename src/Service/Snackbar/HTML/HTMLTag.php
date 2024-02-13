<?php

namespace App\Service\Snackbar\HTML;

abstract class HTMLTag
{
    protected string $class;

    public function __construct(
        array $class = [],
        protected string $id = "",
    )
    {
        $this->class = implode(" ", $class);
    }

    abstract public function htmlCode(): string;
}