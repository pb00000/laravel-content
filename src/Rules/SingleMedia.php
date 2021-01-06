<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class SingleMedia implements Rule
{
    use ValidatesAttributes;

    private $allowedMimes;

    public function __construct(array $allowedMimes = [])
    {
        $this->allowedMimes = $allowedMimes;
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
            return app($value['media_repository_class'])->exists($value);
        }

        return false;
    }

    private function fileInstancePasses($attribute, $value): bool
    {
        if (!$this->validateFile($attribute, $value)) {
            return false;
        }

        if (!empty($this->allowedMimes)) {
            return $this->validateMimes($attribute, $value, $this->allowedMimes);
        }

        return true;
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
