<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Middleware;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;

class MiddlewareHandler
{
    protected $middleware = [];
    protected $passable;

    public function setPassable(...$passable): self
    {
        $this->passable = $passable;

        return $this;
    }

    public function withMiddleware(array $middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function execute(...$passable)
    {
        $passable = func_num_args() ? $passable : $this->passable;

        $middleware = array_map(function ($middleware) {
            return new MiddlewareThroughPipeline($middleware);
        }, $this->middleware);

        $result = (new Pipeline)
            ->send($passable)
            ->through($middleware)
            ->via('execute')
            ->thenReturn();

        return Arr::wrap($result);
    }
}
