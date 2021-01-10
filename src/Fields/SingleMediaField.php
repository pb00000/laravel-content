<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Fields;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\ForwardsCalls;
use ProtoneMedia\LaravelContent\Middleware\ResolveMedia;
use ProtoneMedia\LaravelContent\Rules\SingleMedia;

abstract class SingleMediaField extends Field implements Arrayable, Jsonable
{
    use ForwardsCalls;
    use InteractsWithMediaRepository;

    protected $media;

    public function allowedMimes(): array
    {
        return [];
    }

    public static function prepareRequestForValidation($key, Request $request = null)
    {
        $request = $request ?: request();

        $input = $request->input($key);

        if (!is_array($input)) {
            return;
        }

        $repository = static::resolveDefaultRepository();

        $media = $repository->find($input);

        if (!$media) {
            return;
        }

        $request->replace([
            $key => $repository->getFile($media),
        ]);
    }

    public function makeSingleMediaRule(): SingleMedia
    {
        return new SingleMedia($this->allowedMimes());
    }

    public function defaultRules(): array
    {
        return [$this->makeSingleMediaRule()];
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

    public static function fromDatabase($model, string $key, $value, array $attributes)
    {
        $value = json_decode($value, true);

        if (!$value) {
            return new static;
        }

        $repository = app($value['media_repository_class']);

        $media = $repository->find($value);

        return new static($media, $repository);
    }

    private static function model($model): Model
    {
        return $model;
    }

    public function toDatabase($model, string $key, array $attributes)
    {
        $json = $this->toJson();

        static::model($model)->saved(function (Model $savedModel) use ($model) {
            if ($savedModel->isNot($model)) {
                return;
            }

            $this->repository->attachToModel($this->media, $savedModel);
        });

        return $json;
    }

    public function toArray()
    {
        if (!$this->media) {
            return null;
        }

        return array_merge(
            $this->repository->toArray($this->media),
            ['media_repository_class' => get_class($this->repository)]
        );
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    //

    public static function defaultInputMiddleware(): array
    {
        return [
            new ResolveMedia(static::resolveDefaultRepository()),
        ];
    }

    public static function empty(): self
    {
        return app()->makeWith(static::class, [
            'repository' => static::resolveDefaultRepository(),
        ]);
    }

    //

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
