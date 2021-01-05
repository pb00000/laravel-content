<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use ProtoneMedia\LaravelContent\Media\MediaRepository;
use ProtoneMedia\LaravelContent\Rules\SingleMedia;

class Image extends SingleMediaField
{
    public function __construct($media = null, MediaRepository $repository = null)
    {
        $this->media = $media;

        $this->repository = $repository ?: static::resolveDefaultRepository();
    }

    public function defaultRules(): array
    {
        return [new SingleMedia(['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])];
    }
}
