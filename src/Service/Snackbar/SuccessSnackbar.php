<?php

namespace App\Service\Snackbar;

use App\Service\Snackbar\HTML\Div;
use App\Service\Snackbar\HTML\Img;
use App\Service\Snackbar\HTML\Span;

class SuccessSnackbar extends AbstractSnackbar
{

    /**
     * Une snackbar de succès.
     * @return string
     */
    public function htmlCode(): string
    {
        /*
        <div class="snackbar success show">
            <div>
                <span class="snackbar-title white">Confirmation</span>
                <span class="snackbar-message white">Your changes have been saved.</span>
            </div>
            <img alt="check-mark" src="{{ asset('assets/check_mark.svg') }}" width="32" height="32">
        </div>
        */
        $snackbar_warning = new Div(["snackbar", "success", "show"]);
        $snackbar_text = new Div();
        $snackbar_title = new Span(["snackbar-title", "white"]);
        $snackbar_title->setContent([$this->title]);
        $snackbar_message = new Span(["snackbar-message", "white"]);
        $snackbar_message->setContent([$this->message]);
        $snackbar_text->setContent([$snackbar_title, $snackbar_message]);
        $successIcon = new Img("check-mark", "/assets/check_mark.svg", 32, 32);
        $snackbar_warning->setContent([$snackbar_text, $successIcon]);
        return $snackbar_warning->htmlCode();
    }
}