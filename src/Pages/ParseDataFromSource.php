<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Support\Arr;

class ParseDataFromSource
{
    protected $data = [];

    public function parse($source, array $fields = [], $keyPrefix = null): array
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                return $this->parse($source, $field, "{$key}.");
            }

            $value = data_get($source, $key);

            Arr::set(
                $this->data,
                ($keyPrefix . $key),
                $field::fromInput($value)->resolve()
            );
        }

        return tap($this->data, function () {
            $this->data = [];
        });
    }
}
