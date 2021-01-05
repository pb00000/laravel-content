<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelContent\Models\TemporaryMediaLibraryMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdderFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\File;

class MediaLibraryRepository implements MediaRepository
{
    public function find(array $data)
    {
        return Media::find($data['key']);
    }

    public function findOrFail(array $data)
    {
        return Media::findOrFail($data['key']);
    }

    public function exists(array $data): bool
    {
        return Media::whereKey($data['key'])->exists();
    }

    public function toHtml($value): string
    {
        return $value->toHtml();
    }

    public function getFile($value): File
    {
        return new File($value->getPath());
    }

    public function toArray($value): array
    {
        return [
            'key' => $value->getKey(),
        ];
    }

    public function fromRequest(Request $request): MediaRequest
    {
        return new MediaRequest($this, $request);
    }

    public function storeTemporarily($file)
    {
        return FileAdderFactory::create(TemporaryMediaLibraryMedia::create(), $file)->toMediaLibrary();
    }

    public function attachToModel($value, Model $model)
    {
        if ($value->model_type == $model->getMorphClass() && $value->model_id == $model->getKey()) {
            return;
        }

        $value->model_type = $model->getMorphClass();
        $value->model_id   = $model->getKey();
        $value->save();
    }
}
