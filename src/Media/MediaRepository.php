<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface MediaRepository
{
    public function find(array $data);

    public function exists(array $data): bool;

    public function toHtml($value): string;

    public function toArray($value): array;

    public function fromRequest(Request $request): MediaRequest;

    public function storeTemporarily($file);

    public function attachToModel($value, Model $model);
}
