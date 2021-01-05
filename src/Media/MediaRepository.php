<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Media;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\File;

interface MediaRepository
{
    public function find(array $data);

    public function findOrFail(array $data);

    public function exists(array $data): bool;

    public function toHtml($value): string;

    public function toArray($value): array;

    public function getFile($value): File;

    public function storeTemporarily($file);

    public function attachToModel($value, Model $model);
}
