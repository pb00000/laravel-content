<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Media;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelContent\Fields\Field;

class MediaRequest
{
    private MediaRepository $repository;
    private Request $request;
    private $fieldClass;

    public function __construct(MediaRepository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->request    = $request;
    }

    public function prepareForValidation($key)
    {
        if ($this->request->file($key)) {
            return;
        }

        $media = $this->getExistingMedia($key);

        $this->request->replace([
            $key => $media ? $this->repository->getFile($media) : null,
        ]);
    }

    public function getExistingMedia($key): ?Field
    {
        $input = $this->request->input($key);

        if (!is_array($input)) {
            return null;
        }

        $media = Container::getInstance()
            ->make($input['media_repository_class'])
            ->find($input);

        return $media
            ? $this->resolveMediaIntoField($media)
            : null;
    }

    public function get($key): Field
    {
        if ($file = $this->request->file($key)) {
            return $this->resolveMediaIntoField(
                $this->repository->storeTemporarily($file)
            );
        }

        return $this->getExistingMedia($key);
    }

    private function resolveMediaIntoField($media)
    {
        return Container::getInstance()->makeWith($this->fieldClass, [
            'media'      => $media,
            'repository' => $this->repository,
        ]);
    }

    public function setFieldClass($fieldClass): self
    {
        $this->fieldClass = $fieldClass;

        return $this;
    }
}
