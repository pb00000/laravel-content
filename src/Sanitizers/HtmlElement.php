<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

class HtmlElement
{
    private $element;
    private $attributes = [];

    private $_type            = 'Block';
    private $_contents        = 'Flow';
    private $_attrCollections = 'Common';
    private $_attributes      = [];

    public function __construct(string $element)
    {
        $this->element = $element;
    }

    public function getElement(): string
    {
        return $this->element;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function _getType(): string
    {
        return $this->_type;
    }

    public function _getContents(): string
    {
        return $this->_contents;
    }

    public function _getAttrCollections(): string
    {
        return $this->_attrCollections;
    }

    public function _getAttributes(): array
    {
        return $this->_attributes;
    }

    public function allowAttribute($attribute, ...$arguments): self
    {
        $this->attributes[] = $attribute instanceof HtmlAttribute
            ? $attribute
            : new HtmlAttribute($attribute, ...$arguments);

        return $this;
    }

    public static function make(string $element): self
    {
        return new static($element);
    }
}
