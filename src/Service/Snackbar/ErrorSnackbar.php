<?php

namespace App\Service\Snackbar;

use App\Service\Snackbar\HTML\Button;
use App\Service\Snackbar\HTML\Div;
use App\Service\Snackbar\HTML\Img;
use App\Service\Snackbar\HTML\Span;

class ErrorSnackbar extends AbstractSnackbar
{
    /**
     * Une snackbar d'erreur.
     * Elle possède une croix pour pouvoir la fermer
     * @return string
     */
    public function htmlCode(): string
    {
        /*
        <div class="snackbar error show">
            <div>
                <span class="snackbar-title">Holy smokes !</span>
                <span class="snackbar-message">Something seriously bad happened.</span>
            </div>
            <button class="close-button" type="button">
                <img alt="cross" src="{{ asset('assets/cross.svg') }}" width="16" height="16">
            </button>
        </div>
        */
        $snackbar_error = new Div(["snackbar", "error", "show"]);
        $snackbar_text = new Div();
        $snackbar_title = new Span(["snackbar-title"]);
        $snackbar_title->setContent([$this->title]);
        $snackbar_message = new Span(["snackbar-message"]);
        $snackbar_message->setContent([$this->message]);
        $snackbar_text->setContent([$snackbar_title, $snackbar_message]);
        $close_button = new Button(["close-button"]);
        $crossImg = new Img("cross", "/assets/cross.svg", 16, 16);
        $close_button->setContent([$crossImg]);
        $snackbar_error->setContent([$snackbar_text, $close_button]);
        return $snackbar_error->htmlCode();
    }
}