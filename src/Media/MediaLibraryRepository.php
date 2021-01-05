<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryRepository implements MediaRepository
{
    public function find(array $data)
    {
        return Media::findOrFail($data['key']);
    }

    public function toHtml($value): string
    {
        return $value->toHtml();
    }

    public function toArray($value): array
    {
        return [
            'key' => $value->getKey(),
        ];
    }
}
