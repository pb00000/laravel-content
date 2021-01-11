<?php

namespace ProtoneMedia\LaravelContent;

use HTMLPurifier_Config;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use ProtoneMedia\LaravelContent\Media\MediaLibraryRepository;
use ProtoneMedia\LaravelContent\Media\MediaRepository;
use ProtoneMedia\LaravelContent\Sanitizers\HtmlPurifier;
use ProtoneMedia\LaravelContent\Sanitizers\HtmlSanitizer;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the shared binding.
     */
    public function register()
    {
        $this->app->bind(MediaRepository::class, function () {
            return new MediaLibraryRepository;
        });

        $this->app->bind(HtmlSanitizer::class, function () {
            return (new HtmlPurifier)->withConfig(function (HTMLPurifier_Config $config) {
                $cachePath = storage_path('laravel-content/html-purifier-cache');

                $mode = 0755;

                if (!file_exists($cachePath)) {
                    mkdir($cachePath, $mode, true);
                }

                $config->loadArray([
                    'Cache.SerializerPath'        => $cachePath,
                    'Cache.SerializerPermissions' => $mode,
                ]);
            });
        });
    }
}
