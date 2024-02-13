<?php

namespace App\Service\Snackbar\HTML;

class Div extends HTMLTagWithContent
{
    public function htmlCode(): string
    {
        return "<div id='$this->id' class='$this->class'>".parent::htmlCode()."</div>";
    }
}