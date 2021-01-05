<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use ProtoneMedia\LaravelContent\Media\MediaRepository;

class Image extends SingleMediaField
{
    public function __construct($media = null, MediaRepository $repository = null)
    {
        $this->media = $media;

        $this->repository = $repository ?: static::resolveDefaultRepository();
    }
}
