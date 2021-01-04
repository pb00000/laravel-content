<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Middleware;

class ConvertEmptyStringToNull
{
    public function execute($value = null)
    {
        return is_string($value) && $value === '' ? null : $value;
    }
}
