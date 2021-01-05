<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Htmlable;

abstract class Field implements Htmlable, Castable
{
    protected $rules;

    public static function defaultInputMiddleware(): array
    {
        return [];
    }

    public static function fromInput(...$arguments): FromInput
    {
        return (new FromInput(static::class, ...$arguments))
            ->withMiddleware(static::defaultInputMiddleware());
    }

    //

    public static function castUsing(array $arguments)
    {
        return new EloquentCaster(static::class);
    }

    abstract public static function fromDatabase($model, string $key, $value, array $attributes);

    abstract public function toDatabase($model, string $key, array $attributes);

    //

    public function defaultRules(): array
    {
        return [];
    }

    public function getRules(): array
    {
        if (!is_array($this->rules)) {
            return $this->defaultRules();
        }

        return $this->rules;
    }

    public function mergeRules(array $rules): self
    {
        return $this->setRules(array_merge($this->getRules(), $rules));
    }

    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }
}
