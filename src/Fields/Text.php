<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use ProtoneMedia\LaravelContent\Middleware\ConvertEmptyStringToNull;
use ProtoneMedia\LaravelContent\Middleware\TrimString;

class Text extends Field implements Htmlable
{
    public function __construct(string $value = null)
    {
        $this->value = $value;
    }

    public static function fromDatabase($model, string $key, $value, array $attributes)
    {
        return new static($value);
    }

    public function toDatabase($model, string $key, array $attributes)
    {
        return $this->value;
    }

    //

    public static function defaultInputMiddleware(): array
    {
        return [
            TrimString::class,
            ConvertEmptyStringToNull::class,
        ];
    }

    public function defaultRules(): array
    {
        return ['string'];
    }

    public function getValue(): string
    {
        return $this->value ?: '';
    }

    public function toHtml(): HtmlString
    {
        return new HtmlString(
            is_null($this->value) ? $this->value : null
        );
    }
}
