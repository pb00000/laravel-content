<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Middleware;

use ProtoneMedia\LaravelContent\Sanitizers\HtmlSanitizer;

class SanitizeHtml
{
    private HtmlSanitizer $htmlSanitizer;

    public function __construct(HtmlSanitizer $htmlSanitizer)
    {
        $this->htmlSanitizer = $htmlSanitizer;
    }

    public function execute($value = null)
    {
        return $this->htmlSanitizer->execute($value);
    }
}
