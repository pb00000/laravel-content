<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Support\Traits\ForwardsCalls;
use ProtoneMedia\LaravelContent\Middleware\MiddlewareHandler;

class FieldResolver
{
    use ForwardsCalls;

    private $middlewareHandler;
    private $fieldClass;
    private $resolved;

    public function __construct(MiddlewareHandler $middlewareHandler, $fieldClass)
    {
        $this->middlewareHandler = $middlewareHandler;
        $this->fieldClass        = $fieldClass;
    }

    public function withMiddleware(array $middleware): self
    {
        $this->middlewareHandler->withMiddleware($middleware);

        return $this;
    }

    public function resolve(): Field
    {
        if ($this->resolved) {
            return $this->resolved;
        }

        return $this->resolved = new $this->fieldClass(
            ...$this->middlewareHandler->execute()
        );
    }

    /**
     * Handle dynamic method calls into the field.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->resolve(), $method, $parameters);
    }
}
