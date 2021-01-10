<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Support\Arr;

class ParseDataFromSource
{
    protected $data;

    public function parse($source, array $fields = [], $keyPrefix = null): array
    {
        foreach ($fields as $key => $field) {
            $fullKey = $keyPrefix . $key;

            if (is_array($field)) {
                return $this->parse($source, $field, "{$fullKey}.");
            }

            $value = data_get($source, $fullKey);

            Arr::set(
                $this->data,
                $fullKey,
                $field::fromInput($value)->resolve()
            );
        }

        return $this->data;
    }
}
