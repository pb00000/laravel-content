<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Rules;

use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class SingleMedia implements Rule
{
    use ValidatesAttributes;

    private $allowedMimeTypes;

    public function __construct(array $allowedMimeTypes = [])
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->isValidFileInstance($value)) {
            return $this->fileInstancePasses($attribute, $value);
        }

        if (is_array($value) && array_key_exists('media_repository_class', $value)) {
            return Container::getInstance()
                ->make($value['media_repository_class'])
                ->exists($value);
        }

        return false;
    }

    private function fileInstancePasses($attribute, $value): bool
    {
        if (!$this->validateFile($attribute, $value)) {
            return false;
        }

        if (!empty($this->allowedMimeTypes)) {
            return $this->validateMimetypes($attribute, $value, $this->allowedMimeTypes);
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return trans('validation.image');
    }
}
