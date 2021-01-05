<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Pipeline\Pipeline;
use ProtoneMedia\LaravelContent\Middleware\MiddlewareThroughPipeline;

trait InteractsWithMiddleware
{
    protected $middleware;

    public function withMiddleware(array $middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function sendThroughMiddleware($passable)
    {
        $middleware = array_map(function ($middleware) {
            return new MiddlewareThroughPipeline($middleware);
        }, $this->middleware ?: []);

        return (new Pipeline)
            ->send($passable)
            ->through($middleware)
            ->via('execute')
            ->thenReturn();
    }
}
