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
        if (is_callable($this->middleware)) {
            return ($this->middleware)(...$arguments);
        }

        if (is_object($this->middleware)) {
            return $this->middleware->execute(...$arguments);
        }

        return app($this->middleware)->execute(...$arguments);
    }
}
