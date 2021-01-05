<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ProtoneMedia\LaravelContent\Middleware\MiddlewareHandler;

abstract class Field implements Htmlable, Castable, Jsonable
{
    protected $rules;

    public static function defaultInputMiddleware(): array
    {
        return [];
    }

    public static function fromRequest($key, Request $request = null): FieldResolver
    {
        $request = $request ?: request();

        return static::fromSource($request->all(), $key);
    }

    public static function prepareRequestForValidation($key, Request $request = null)
    {
    }

    public static function fromSource($source, $key): FieldResolver
    {
        return static::fromInput(data_get($source, $key));
    }

    public static function fromInput(...$source): FieldResolver
    {
        $handler = (new MiddlewareHandler)
            ->withMiddleware(static::defaultInputMiddleware())
            ->setPassable(...$source);

        return new FieldResolver($handler, static::class);
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

    public function addToRules($rules): self
    {
        return $this->setRules(array_merge($this->getRules(), Arr::wrap($rules)));
    }

    public function setRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }
}
