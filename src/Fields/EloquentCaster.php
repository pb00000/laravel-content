<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EloquentCaster implements CastsAttributes
{
    private $fieldClass;

    public function __construct($fieldClass)
    {
        $this->fieldClass = $fieldClass;
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $fieldClass = $this->fieldClass;

        return $fieldClass::fromDatabase($model, $key, $value, $attributes);
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value->toDatabase($model, $key, $attributes);
    }
}
