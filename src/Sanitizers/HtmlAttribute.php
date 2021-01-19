<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

class HtmlAttribute
{
    private $attribute;
    private $_def = 'Text';

    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function _getDef()
    {
        return $this->_def;
    }

    public static function make(string $attribute): self
    {
        return new static($attribute);
    }
}
