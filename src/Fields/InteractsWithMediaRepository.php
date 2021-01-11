<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use ProtoneMedia\LaravelContent\Media\MediaRepository;

trait InteractsWithMediaRepository
{
    protected MediaRepository $repository;
    protected static $defaultRepositoryResolver;

    public static function setDefaultRepositoryResolver(callable $resolver)
    {
        static::$defaultRepositoryResolver = $resolver;
    }

    protected static function resolveDefaultRepository(): MediaRepository
    {
        $resolver = static::$defaultRepositoryResolver ?: function () {
            return app(MediaRepository::class);
        };

        return call_user_func($resolver);
    }
}
