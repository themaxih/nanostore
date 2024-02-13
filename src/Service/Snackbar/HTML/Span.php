<?php

namespace App\Service\Snackbar\HTML;

class Span extends HTMLTagWithContent
{
    public function htmlCode(): string
    {
        return "<span id='$this->id' class='$this->class'>".parent::htmlCode()."</span>";
    }
}