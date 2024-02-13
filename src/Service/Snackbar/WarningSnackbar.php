<?php

namespace App\Service\Snackbar;

use App\Service\Snackbar\HTML\Div;
use App\Service\Snackbar\HTML\Img;
use App\Service\Snackbar\HTML\Span;

class WarningSnackbar extends AbstractSnackbar
{

    /**
     * Une snackbar d'avertissement
     * Elle se ferme automatiquement au bout de 10 secondes et
     * ne possède pas de croix pour la fermer manuellement
     * Elle possède un petit icone sur la gauche
     * @return string
     */
    public function htmlCode(): string
    {
        /*
        <div class="snackbar warning show">
            <img alt="info-icon" src="{{ asset('assets/info-icon.svg') }}" width="24" height="24">
            <div>
                <span class="snackbar-title">Our privacy policy has changed</span>
                <span class="snackbar-message">Make sure you know how these changes affect you.</span>
            </div>
        </div>
        */
        $snackbar_warning = new Div(["snackbar", "warning", "show"]);
        $warningIcon = new Img("info-icon", "/assets/info-icon.svg", 24, 24);
        $snackbar_text = new Div();
        $snackbar_title = new Span(["snackbar-title"]);
        $snackbar_title->setContent([$this->title]);
        $snackbar_message = new Span(["snackbar-message"]);
        $snackbar_message->setContent([$this->message]);
        $snackbar_text->setContent([$snackbar_title, $snackbar_message]);
        $snackbar_warning->setContent([$warningIcon, $snackbar_text]);
        return $snackbar_warning->htmlCode();
    }
}