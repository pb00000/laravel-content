<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;

class FromRequest implements ExtractFieldFromRequest
{
    use ForwardsCalls;
    use InteractsWithMiddleware;

    protected $fieldClass;
    protected $request;
    protected $arguments;

    public function __construct($fieldClass, Request $request, ...$arguments)
    {
        $this->fieldClass = $fieldClass;
        $this->request    = $request;
        $this->arguments  = $arguments;
    }

    public function resolve($key): Field
    {
        $input = $this->request->input($key);

        $arguments = array_merge([$input], $this->arguments);

        $result = Arr::wrap(
            $this->sendThroughMiddleware($arguments)
        );

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
