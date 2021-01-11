<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use ProtoneMedia\LaravelContent\Middleware\ConvertEmptyStringToNull;
use ProtoneMedia\LaravelContent\Middleware\TrimString;

class Html extends Field implements Htmlable
{
    protected $value;

    public function __construct(string $value = null)
    {
        $this->value = $value;
    }

    public static function fromDatabase($model, string $key, $value, array $attributes)
    {
        return new static($value);
    }

    public function toDatabase($model = null, string $key = null, array $attributes = null)
    {
        return $this->value;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->value, $options);
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
        // $content = Purifier::clean($content ?: '', $config);

        //  mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');

        return new HtmlString(
            is_null($this->value) ? $this->value : null
        );
    }
}
