<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Support\Arr;

class ParseDataFromSource
{
    protected $data;

    public function parse($source, array $fields = [], $keyPrefix = null): array
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $this->parse($source, $field, "{$keyPrefix}{$key}.");
                continue;
            }

            $value = data_get($source, "{$keyPrefix}{$key}");

            Arr::set(
                $this->data,
                "{$keyPrefix}{$key}",
                $field::fromInput($value)->resolve()
            );
        }

        return $this->data;
    }
}
