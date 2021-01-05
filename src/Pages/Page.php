<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

abstract class Page
{
    use LoadsDataFromRequest;

    protected $data;

    abstract public function fields(): array;

    public function saveAsJson($model, $key)
    {
        $model->$key = collect($this->data)->toJson();
        $model->save();

        return $model;
    }
}
