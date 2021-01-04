<?php

namespace ProtoneMedia\LaravelContent;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the shared binding.
     */
    public function register()
    {
        $this->app->singleton('laravel-content', function () {
        });
    }
}
