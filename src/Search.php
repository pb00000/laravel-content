<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent;

use Illuminate\Support\Facades\Facade;

class Search extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-content';
    }
}
