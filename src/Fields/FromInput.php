<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;
use ProtoneMedia\LaravelContent\Middleware\MiddlewareThroughPipeline;

class FromInput
{
    use ForwardsCalls;

    protected $fieldClass;
    protected $arguments;
    protected $middleware;

    public function __construct($fieldClass, ...$arguments)
    {
        $this->fieldClass = $fieldClass;
        $this->arguments  = $arguments;
    }

    public function withMiddleware(array $middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function resolve()
    {
        $middleware = array_map(function ($middleware) {
            return new MiddlewareThroughPipeline($middleware);
        }, $this->middleware ?: []);

        $result = app(Pipeline::class)
            ->send($this->arguments)
            ->through($middleware)
            ->via('execute')
            ->thenReturn();

        $result = Arr::wrap($result);

        $fieldClass = $this->fieldClass;

        return new $fieldClass(...$result);
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
