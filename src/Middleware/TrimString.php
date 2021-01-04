<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Middleware;

class TrimString
{
    public function execute($value = null)
    {
        return is_string($value) ? trim($value) : $value;
    }
}
