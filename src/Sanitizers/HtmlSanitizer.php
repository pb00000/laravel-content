<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

interface HtmlSanitizer
{
    public function execute($value = null): ?string;
}
