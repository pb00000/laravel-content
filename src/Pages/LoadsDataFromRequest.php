<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Pages;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait LoadsDataFromRequest
{
    public function parseArrayFromRequest(Request $request, array $data = [], $keyPrefix = null)
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

    public function setDataFromRequest(Request $request): self
    {
        return $this->parseArrayFromRequest($request, $this->fields());
    }

    public static function fromRequest(Request $request = null): self
    {
        return app(static::class)->setDataFromRequest($request ?: request());
    }
}
