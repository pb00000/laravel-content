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

    public static function fromInput(...$arguments): FromInput
    {
        return (new FromInput(static::class, ...$arguments))->withMiddleware([
            TrimString::class,
            ConvertEmptyStringToNull::class,
        ]);
    }

    public static function fromDatabase($model, string $key, $value, array $attributes)
    {
        return new static($value);
    }

    public function toDatabase($model, string $key, array $attributes)
    {
        return $this->value;
    }

    public function defaultRules(): array
    {
        return ['string'];
    }

    public function getRawValue()
    {
        return $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toHtml(): HtmlString
    {
        return new HtmlString(
            is_null($this->value) ? $this->value : null
        );
    }
}
