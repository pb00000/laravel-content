<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Support\Arr;

class ParseDataFromInput
{
    protected $data;

    public function parse($target, array $fields = []): array
    {
        foreach (Arr::dot($fields) as $key => $field) {
            $value = data_get($target, $key);

            Arr::set(
                $this->data,
                $key,
                $field::fromInput($value)->resolve()
            );
        }

        return $this->data;
    }
}
