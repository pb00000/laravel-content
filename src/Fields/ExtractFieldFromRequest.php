<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

interface ExtractFieldFromRequest
{
    public function resolve($key);
}
