<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;

class FromInput
{
    use ForwardsCalls;
    use InteractsWithMiddleware;

    protected $fieldClass;
    protected $arguments;

    public function __construct($fieldClass, ...$arguments)
    {
        $this->fieldClass = $fieldClass;
        $this->arguments  = $arguments;
    }

    public function resolve()
    {
        $result = Arr::wrap($this->sendThroughMiddleware($this->arguments));

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
