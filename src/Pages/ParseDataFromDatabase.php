<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ParseDataFromDatabase
{
    protected $data;

    public function parse(Model $model, $source, array $fields = [], $keyPrefix = null): array
    {
        foreach ($fields as $key => $field) {
            if (!is_array($field) && $keyPrefix && $source instanceof Model) {
                $keys = explode('.', "{$keyPrefix}{$key}");

                $topLevelKey = array_shift($keys);

                $value = data_get(
                    json_decode($source->{$topLevelKey}, true),
                    implode('.', $keys)
                );

                Arr::set(
                    $this->data,
                    "{$keyPrefix}{$key}",
                    $field::fromDatabase($model, "{$keyPrefix}{$key}", $value, [])
                );

                continue;
            }

            if (is_array($field)) {
                if ($keyPrefix && $source instanceof Model) {
                    $keys = explode('.', "{$keyPrefix}{$key}");

                    $topLevelKey = array_shift($keys);

                    $this->parse(
                        $model,
                        json_decode($source->{$topLevelKey}, true),
                        $field,
                        implode('.', $keys) . '.'
                    );

                    continue;
                }

                $this->parse(
                    $model,
                    $source,
                    $field,
                    "{$keyPrefix}{$key}."
                );

                continue;
            }

            $value = data_get($source, "{$keyPrefix}{$key}");

            Arr::set(
                $this->data,
                "{$keyPrefix}{$key}",
                $field::fromDatabase($model, "{$keyPrefix}{$key}", $value, [])
            );
        }

        return $this->data;
    }
}
