<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

use HTMLPurifier_ChildDef_List;

class DefaultAllowedElements
{
    public static function get(): array
    {
        return [
            'a'          => HtmlElement::make('a')->inlineContent()->inlineChildrenAllowed(),
            'address'    => HtmlElement::make('address')->blockContent()->inlineChildrenAllowed(),
            'article'    => HtmlElement::make('article')->blockContent()->anyChildrenAllowed(),
            'aside'      => HtmlElement::make('aside')->blockContent()->anyChildrenAllowed(),
            'b'          => HtmlElement::make('b')->inlineContent()->inlineChildrenAllowed(),
            'blockquote' => HtmlElement::make('blockquote')->blockContent(),
            'br'         => HtmlElement::make('br')->inlineContent()->noChildrenAllowed(),
            'del'        => HtmlElement::make('del')->blockContent(),
            'div'        => HtmlElement::make('div')->blockContent()->anyChildrenAllowed(),
            'em'         => HtmlElement::make('em')->inlineContent()->inlineChildrenAllowed(),
            'figcaption' => HtmlElement::make('figcaption')->inlineContent(),
            'figure'     => HtmlElement::make('figure')->blockContent(),
            'footer'     => HtmlElement::make('footer')->blockContent()->anyChildrenAllowed(),
            'h1'         => HtmlElement::make('h1')->headingContent()->inlineChildrenAllowed(),
            'h2'         => HtmlElement::make('h2')->headingContent()->inlineChildrenAllowed(),
            'h3'         => HtmlElement::make('h3')->headingContent()->inlineChildrenAllowed(),
            'h4'         => HtmlElement::make('h4')->headingContent()->inlineChildrenAllowed(),
            'h5'         => HtmlElement::make('h5')->headingContent()->inlineChildrenAllowed(),
            'h6'         => HtmlElement::make('h6')->headingContent()->inlineChildrenAllowed(),
            'header'     => HtmlElement::make('header')->blockContent()->anyChildrenAllowed(),
            'hgroup'     => HtmlElement::make('hgroup')->blockContent(),
            'i'          => HtmlElement::make('i')->inlineContent()->inlineChildrenAllowed(),
            'iframe'     => HtmlElement::make('iframe'),
            'img'        => HtmlElement::make('img')->allowAttribute('src'),
            'ins'        => HtmlElement::make('ins')->blockContent(),
            'li'         => HtmlElement::make('li')->manualContent()->anyChildrenAllowed(),
            'mark'       => HtmlElement::make('mark')->inlineContent(),
            'nav'        => HtmlElement::make('nav')->blockContent()->anyChildrenAllowed(),
            'ol'         => HtmlElement::make('ol')->listContent()->withAllowedChildren(new HTMLPurifier_ChildDef_List),
            'p'          => HtmlElement::make('p')->blockContent()->inlineChildrenAllowed(),
            's'          => HtmlElement::make('s')->inlineContent(),
            'section'    => HtmlElement::make('section')->blockContent()->anyChildrenAllowed(),
            'source'     => HtmlElement::make('source')->blockContent(),
            'span'       => HtmlElement::make('span')->inlineContent()->inlineChildrenAllowed(),
            'strong'     => HtmlElement::make('strong')->inlineContent()->inlineChildrenAllowed(),
            'sub'        => HtmlElement::make('sub')->inlineContent()->inlineChildrenAllowed(),
            'sup'        => HtmlElement::make('sup')->inlineContent()->inlineChildrenAllowed(),
            'table'      => HtmlElement::make('table'),
            'td'         => HtmlElement::make('td'),
            'th'         => HtmlElement::make('th'),
            'tr'         => HtmlElement::make('tr'),
            'u'          => HtmlElement::make('u')->inlineContent(),
            'ul'         => HtmlElement::make('ul')->listContent()->withAllowedChildren(new HTMLPurifier_ChildDef_List),
            'var'        => HtmlElement::make('var')->inlineContent(),
            'video'      => HtmlElement::make('video')->blockContent(),
            'wbr'        => HtmlElement::make('wbr')->inlineContent(),
        ];
    }
}
