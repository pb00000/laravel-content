<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use ProtoneMedia\LaravelContent\Sanitizers\HtmlSanitizer;

trait InteractsWithHtmlSanitizer
{
    protected HtmlSanitizer $htmlSanitizer;
    protected static $defaultHtmlSanitizerResolver;

    public static function setDefaultHtmlSanitizerResolver(callable $resolver)
    {
        static::$defaultHtmlSanitizerResolver = $resolver;
    }

    protected static function resolveDefaultHtmlSanitizer(): HtmlSanitizer
    {
        $resolver = static::$defaultHtmlSanitizerResolver ?: function () {
            return app(HtmlSanitizer::class);
        };

        return call_user_func($resolver);
    }
}
