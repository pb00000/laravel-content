<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Http\Request;

abstract class Page
{
    protected $data;

    abstract public function fields(): array;

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public static function loadFromRequest(Request $request = null): Page
    {
        $request = $request ?: request();

        $page = app(static::class);

        $data = (new ParseDataFromSource)->parse(
            $request->all(),
            $fields = $page->fields()
        );

        return $page->setData($data);
    }

    public function saveAsJson($model, $key)
    {
        $model->$key = collect($this->data)->toJson();
        $model->save();

        return $model;
    }
}
