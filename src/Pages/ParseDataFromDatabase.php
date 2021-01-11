<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ParseDataFromDatabase
{
    protected $data;

    public function parse(Model $model, $target, array $fields = []): array
    {
        foreach (Arr::dot($fields) as $key => $field) {
            $value = static::get($target, $key);

            Arr::set(
                $this->data,
                $key,
                $field::fromDatabase($model, $key, $value, [])
            );
        }

        return $this->data;
    }

    private static function get($target, $key)
    {
        if ($target instanceof Model && Str::contains($key, '.')) {
            $keys = explode('.', $key);

            $topLevelKey = array_shift($keys);

            $target = json_decode($target->{$topLevelKey}, true);

            $key = implode('.', $keys);
        }

        return data_get($target, $key);
    }
}
