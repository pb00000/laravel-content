<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

        $data = app(ParseDataFromSource::class)->parse($request->all(), $page->fields());

        return $page->setData($data);
    }

    public function saveToModel(Model $model)
    {
        static::wrapArraysInCollections($this->data)->each(function ($value, $key) use ($model) {
            $model->{$key} = $value->toJson();
        });

        return tap($model)->save();
    }

    public function saveAsJson(Model $model, $key)
    {
        $model->{$key} = static::wrapArraysInCollections($this->data)->toJson();
        $model->save();

        return $model;
    }

    private static function wrapArraysInCollections(array $data): Collection
    {
        foreach ($data as $key => $value) {
            $data[$key] = is_array($value)
                    ? Collection::make(static::wrapArraysInCollections($value))
                    : $value;
        }

        return Collection::make($data);
    }
}
