<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

use HTMLPurifier_ChildDef;

class HtmlElement
{
    private string $element;
    private $attributes = [];

    /**
     * What content set should element be registered to? Set as false to skip this step.
     *
     * The HTML specification defines two major content sets: Inline and Block. Each of
     * these content sets contain a list of elements: Inline contains things like span
     * and b while Block contains things like div and blockquote.
     *
     * These content sets amount to a macro mechanism for HTML definition. Most elements in HTML
     * are organized into one of these two sets, and most elements in HTML allow elements from
     * one of these sets. If we had to write each element verbatim into each other element's
     * allowed children, we would have ridiculously large lists; instead we use content
     * sets to compactify the declaration.
     *
     * By specifying a valid value here, all other elements that use that content set will also
     * allow your element, without you having to do anything. If you specify false, you'll
     * have to register your element manually.
     *
     * @var string|bool
     */
    private $contentSet = 'Block';

    /**
     * Allowed children in form of: "$content_model_type: $content_model"
     *
     * Allowed children defines the elements that this element can contain. The allowed
     * values may range from none to a complex regexp depending on your element.
     *
     * @var string|HTMLPurifier_ChildDef
     */
    private $allowedChildren = 'Flow';

    /**
     * What attribute collections to register to element?
     *
     * @var array|string
     */
    private $attributeCollection = 'Common';

    /**
     * What unique attributes does the element define?
     *
     * @var array
     */
    private $_attributes = [];

    public function __construct(string $element)
    {
        $this->element = $element;
    }

    //

    public function getElement(): string
    {
        return $this->element;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    //

    public function getContentSet()
    {
        return $this->contentSet;
    }

    /**
     * Block-like elements, like paragraphs and lists
     * http://htmlpurifier.org/docs/enduser-customize.html
     *
     * @return self
     */
    public function blockContent(): self
    {
        $this->contentSet = 'Block';

        return $this;
    }

    /**
     * @return self
     */
    public function headingContent(): self
    {
        $this->contentSet = 'Heading';

        return $this;
    }

    /**
     * @return self
     */
    public function listContent(): self
    {
        $this->contentSet = 'List';

        return $this;
    }

    /**
     * Character level elements, text
     * http://htmlpurifier.org/docs/enduser-customize.html
     *
     * @return self
     */
    public function inlineContent(): self
    {
        $this->contentSet = 'Inline';

        return $this;
    }

    /**
     * Any element that doesn't fit into the mold, for example li or tr
     * http://htmlpurifier.org/docs/enduser-customize.html
     *
     * @return self
     */
    public function manualContent(): self
    {
        $this->contentSet = false;

        return $this;
    }

    //

    public function getAllowedChildren()
    {
        return $this->allowedChildren;
    }

    public function childrenAllowed(HTMLPurifier_ChildDef $allowedChildren): self
    {
        $this->allowedChildren = $allowedChildren;

        return $this;
    }

    public function noChildrenAllowed(): self
    {
        $this->allowedChildren = 'Empty';

        return $this;
    }

    public function inlineChildrenAllowed(): self
    {
        $this->allowedChildren = 'Inline';

        return $this;
    }

    public function anyChildrenAllowed(): self
    {
        $this->allowedChildren = 'Flow';

        return $this;
    }

    //

    public function getAttributeCollection()
    {
        return $this->attributeCollection;
    }

    public function _getAttributes()
    {
        return $this->_attributes;
    }

    //

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
