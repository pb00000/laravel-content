<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Middleware;

use Illuminate\Http\UploadedFile;
use ProtoneMedia\LaravelContent\Media\MediaRepository;
use Symfony\Component\HttpFoundation\File\File;

class ResolveMedia
{
    private MediaRepository $mediaRepository;

    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function execute($value = null)
    {
        if ($value instanceof UploadedFile || $value instanceof File) {
            return $this->mediaRepository->storeTemporarily($value);
        }

        if (is_array($value)) {
            return $this->mediaRepository->find($value);
        }

        return;
    }
}
