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

    public function get($key): Field
    {
        if ($file = $this->request->file($key)) {
            $media = $this->repository->storeTemporarily($file);

            return Container::getInstance()->makeWith($this->fieldClass, [
                'media'      => $media,
                'repository' => $this->repository,
            ]);
        }
    }

    public function setFieldClass($fieldClass): self
    {
        $this->fieldClass = $fieldClass;

        return $this;
    }
}
