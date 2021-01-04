<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Middleware;

class MiddlewareThroughPipeline
{
    private $middleware;

    public function __construct($middleware)
    {
        $this->middleware = $middleware;
    }

    public function execute($arguments)
    {
        $middleware = $this->middleware;

        if (is_callable($middleware)) {
            return $middleware(...$arguments);
        }

        if (is_object($middleware)) {
            return $middleware->execute(...$arguments);
        }

        return app()->make($middleware)->execute(...$arguments);
    }
}
