<?php

namespace App\Service\Snackbar;

use App\Service\Snackbar\HTML\Div;
use App\Service\Snackbar\HTML\Span;

class InformationsSnackbar extends AbstractSnackbar
{

    /**
     * Une snackbar d'informations
     * Elle se ferme automatiquement au bout de 10 secondes et
     * ne possède pas de croix pour la fermer manuellement
     * @return string
     */
    public function htmlCode(): string
    {
        /*
        <div class="snackbar informations show">
            <span class="snackbar-title">Informational message</span>
            <span class="snackbar-message">Some additional text to explain this message.</span>
        </div>
        */
        $snackbar_info = new Div(["snackbar", "informations", "show"]);
        $snackbar_title = new Span(["snackbar-title"]);
        $snackbar_title->setContent([$this->title]);
        $snackbar_message = new Span(["snackbar-message"]);
        $snackbar_message->setContent([$this->message]);
        $snackbar_info->setContent([$snackbar_title, $snackbar_message]);
        return $snackbar_info->htmlCode();
    }
}