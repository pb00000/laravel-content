<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Media;

interface MediaRepository
{
    public function find(array $data);

    public function toHtml($value): string;

    public function toArray($value): array;
}
