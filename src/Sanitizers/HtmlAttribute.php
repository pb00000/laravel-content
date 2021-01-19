<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

class HtmlAttribute
{
    private $attribute;

    /**
     * Attribute definition, can be string or object, see HTMLPurifier_AttrTypes for details
     *
     * @var mixed
     */
    private $validValues = 'Text';

    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getValidValues()
    {
        return $this->validValues;
    }

    public static function make(string $attribute): self
    {
        return new static($attribute);
    }
}
