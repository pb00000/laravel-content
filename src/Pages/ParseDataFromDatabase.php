<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ParseDataFromDatabase
{
    protected $data;

    public function parse(Model $model, $source, array $fields = []): array
    {
        foreach (Arr::dot($fields) as $key => $field) {
            if ($source instanceof Model && Str::contains($key, '.')) {
                $keys = explode('.', $key);

                $topLevelKey = array_shift($keys);

                $value = data_get(
                    json_decode($source->{$topLevelKey}, true),
                    implode('.', $keys)
                );
            } else {
                $value = data_get($source, $key);
            }

            Arr::set(
                $this->data,
                $key,
                $field::fromDatabase($model, $key, $value, [])
            );
        }

        return $this->data;
    }
}
