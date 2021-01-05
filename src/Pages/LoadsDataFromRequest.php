<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait LoadsDataFromRequest
{
    public function parseArrayFromRequest(Request $request, array $data = [], $keyPrefix = null): Page
    {
        foreach ($data as $key => $field) {
            if (is_array($field)) {
                return $this->parseArrayFromRequest($request, $field, "{$key}.");
            }

            Arr::set(
                $this->data,
                ($keyPrefix . $key),
                $field::fromRequest($request)->resolve($key)
            );
        }

        return $this;
    }

    public static function fromRequest(Request $request = null): Page
    {
        $field = app(static::class);

        return $field->parseArrayFromRequest(
            $request ?: request(),
            $field->fields()
        );
    }
}
