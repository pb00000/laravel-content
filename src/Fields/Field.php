<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Container\Container;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelContent\Middleware\MiddlewareHandler;

abstract class Field implements Htmlable, Castable, Jsonable
{
    use HasRules;

    protected $middleware;

    public static function make(array $parameters = []): self
    {
        return Container::getInstance()->makeWith(static::class, $parameters);
    }

    public static function defaultInputMiddleware(): array
    {
        return [];
    }

    public static function fromRequest($key, Request $request = null): FieldResolver
    {
        $request = $request ?: request();

        return static::fromSource($request->all(), $key);
    }

    public function beforeSaving(): self
    {
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
            ->withPassable(...$source)
            ->withMiddleware(static::defaultInputMiddleware());

        return new FieldResolver($handler, static::class);
    }

    //

    public static function castUsing(array $arguments)
    {
        return new EloquentCaster(static::class);
    }

    abstract public static function fromDatabase($model, string $key, $value, array $attributes);

    abstract public function toDatabase($model = null, string $key = null, array $attributes = null);
}
