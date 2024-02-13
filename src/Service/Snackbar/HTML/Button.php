<?php

namespace App\Service\Snackbar\HTML;

class Button extends HTMLTagWithContent
{
    public function htmlCode(): string
    {
        return "<button id='$this->id' class='$this->class' type='button'>".parent::htmlCode()."</button>";
    }
}