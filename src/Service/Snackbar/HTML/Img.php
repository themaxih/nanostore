<?php

namespace App\Service\Snackbar\HTML;

class Img extends HTMLTag
{
    public function __construct(
        private readonly string $alt,
        private readonly string $src,
        private readonly int $width,
        private readonly int $height,
        array $class = [],
        string $id = ""
    )
    {
        parent::__construct($class, $id);
    }

    public function htmlCode(): string
    {
        return "<img id='$this->id' class='$this->class' alt='$this->alt' src=\"$this->src\" width='$this->width' height='$this->height'>";
    }
}