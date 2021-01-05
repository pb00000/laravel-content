<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ProtoneMedia\LaravelContent\Fields\Field;

abstract class Page
{
    protected $data;

    abstract public function fields(): array;

    public function parseArrayFromRequest(Request $request, array $data = [], $keyPrefix = null)
    {
        foreach ($data as $key => $field) {
            if (is_array($field)) {
                return $this->parseArrayFromRequest($request, $field, "{$key}.");
            }

            Arr::set($this->data, ($keyPrefix . $key), $this->fieldFromRequest($request, $field, $key));
        }

        return $this;
    }

    public function fieldFromRequest(Request $request, Field $field, string $key): Field
    {
        return $field::fromRequest($request)->resolve($key);
    }

    public function setDataFromRequest(Request $request): self
    {
        return $this->parseArrayFromRequest($request, $this->fields());
    }

    public static function fromRequest(Request $request = null): self
    {
        return app(static::class)->setDataFromRequest($request ?: request());
    }

    public function saveAsJson($model, $key)
    {
        $model->$key = collect($this->data)->toJson();
        $model->save();

        return $model;
    }
}
