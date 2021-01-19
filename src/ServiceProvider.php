<?php

namespace ProtoneMedia\LaravelContent;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use ProtoneMedia\LaravelContent\Media\MediaLibraryRepository;
use ProtoneMedia\LaravelContent\Media\MediaRepository;
use ProtoneMedia\LaravelContent\Sanitizers\HtmlPurifier;
use ProtoneMedia\LaravelContent\Sanitizers\HtmlPurifierConfigFactory;
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

        $this->app->bind(HtmlPurifier::class, function () {
            $configFactory = new HtmlPurifierConfigFactory(
                HtmlPurifierConfigFactory::baseConfig()
            );

            return new HtmlPurifier($configFactory->create());
        });

        $this->app->bind(HtmlSanitizer::class, function () {
            return $this->app->make(HtmlPurifier::class);
        });
    }
}
