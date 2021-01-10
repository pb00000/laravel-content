<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use ArrayAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class Page implements ArrayAccess
{
    protected $data;

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function offsetGet($key)
    {
        return Arr::get($this->data, $key);
    }

    public function offsetSet($key, $value)
    {
        Arr::set($this->data, $key, $value);
    }

    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    abstract public function fields(): array;

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public static function loadFromModel(Model $model, $key = null): Page
    {
        $data = $key
            ? json_decode($model->{$key}, true)
            : $model;

        $page = app(static::class);

        $data = app(ParseDataFromDatabase::class)->parse(
            $model,
            $data,
            $page->fields()
        );

        return $page->setData($data);
    }

    public static function loadFromRequest(Request $request = null): Page
    {
        $request = $request ?: request();

        $page = app(static::class);

        $data = app(ParseDataFromSource::class)->parse(
            $request->all(),
            $page->fields()
        );

        return $page->setData($data);
    }

    public function saveToModel(Model $model): Model
    {
        static::wrapArraysInCollections($this->data)->each(function ($value, $key) use ($model) {
            $model->{$key} = $value instanceof Collection
                ? static::mapCollectionIntoDatabase($value, $model)->toJson()
                : $value->toDatabase($model, $key, []);
        });

        return tap($model)->save();
    }

    public function saveAsJson(Model $model, $key): Model
    {
        $collection = static::wrapArraysInCollections($this->data);

        $model->{$key} = static::mapCollectionIntoDatabase($collection, $model)->toJson();

        return tap($model)->save();
    }

    private static function mapCollectionIntoDatabase(Collection $data, Model $model): Collection
    {
        return $data->map(function ($value, $key) use ($model) {
            return $value instanceof Collection
                ? static::mapCollectionIntoDatabase($value, $model)
                : $value->toDatabase($model, $key, []);
        });
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
