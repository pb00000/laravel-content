<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Support\Arr;

class ParseDataFromSource
{
    protected $data;

    public function parse($source, array $fields = []): array
    {
        foreach (Arr::dot($fields) as $key => $field) {
            $value = data_get($source, $key);

            Arr::set(
                $this->data,
                $key,
                $field::fromInput($value)->resolve()
            );
        }

        return $this->data;
    }
}
