<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class Image implements Rule
{
    use ValidatesAttributes;

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

        return true;
    }

    private function fileInstancePasses($attribute, $value)
    {
        if (!$this->validateImage($attribute, $value)) {
            return false;
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
    }
}
