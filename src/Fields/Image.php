<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\ForwardsCalls;
use ProtoneMedia\LaravelContent\Media\MediaLibraryRepository;
use ProtoneMedia\LaravelContent\Media\MediaRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Image extends Field
{
    use ForwardsCalls;

    protected $media;
    protected MediaRepository $repository;

    public function __construct(Media $media = null, $repository = null)
    {
        $this->media = $media;

        $this->repository = $repository ?: new MediaLibraryRepository;
    }

    public static function fromDatabase($model, string $key, $value, array $attributes)
    {
        $value = json_decode($value, true);

        if (!$value) {
            return new static;
        }

        $repository = app($value['repository']);

        $media = $repository->find($value);

        return new static($media, $repository);
    }

    public function toDatabase($model, string $key, array $attributes)
    {
        return json_encode(
            array_merge(
                $this->repository->toArray($this->media),
                ['repository' => get_class($this->repository)]
            )
        );
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function toHtml(): HtmlString
    {
        return new HtmlString(
            $this->repository->toHtml($this->media)
        );
    }

    /**
     * Handle dynamic method calls into the field.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->media, $method, $parameters);
    }
}
