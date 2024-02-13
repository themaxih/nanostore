<?php

namespace App\Service\Snackbar\HTML;

abstract class HTMLTagWithContent extends HTMLTag
{
    protected array $content = [];

    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    public function htmlCode(): string
    {
        $code = "";
        foreach ($this->content as $content) {
            $code .= $content instanceof HTMLTag ? $content->htmlCode() : str_replace("\n", "<br>", $content);
        }
        return $code;
    }
}