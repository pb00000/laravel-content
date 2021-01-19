<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

use HTMLPurifier_ChildDef_List;
use HTMLPurifier_Config;
use HTMLPurifier_HTMLDefinition;
use Illuminate\Support\Collection;

class HtmlPurifierConfigFactory
{
    private $config;

    private HTMLPurifier_HTMLDefinition $htmlDefinition;

    private $allowedElements;

    public function __construct(HTMLPurifier_Config $config = null)
    {
        $this->initAllowedElements();

        $this->config = HTMLPurifier_Config::createDefault();

        if (!is_null($config)) {
            $allConfig = $config->getAll();
            $allConfig['HTML']['DefinitionRev']++;

            $this->config->loadArray($allConfig);
        }
    }

    public static function baseConfig(): HTMLPurifier_Config
    {
        return tap(HTMLPurifier_Config::createDefault(), function (HTMLPurifier_Config $config) {
            $cachePath = storage_path('laravel-content/html-purifier-cache');

            $mode = 0755;

            if (!file_exists($cachePath)) {
                mkdir($cachePath, $mode, true);
            }

            $config->loadArray([
                'Cache.SerializerPath'        => $cachePath,
                'Cache.SerializerPermissions' => $mode,
                'HTML.DefinitionID'           => static::class,
                'HTML.DefinitionRev'          => 1,
            ]);
        });
    }

    private function initAllowedElements()
    {
        $this->allowedElements = [
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
            'ol'         => HtmlElement::make('ol')->listContent()->childrenAllowed(new HTMLPurifier_ChildDef_List),
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
            'ul'         => HtmlElement::make('ul')->listContent()->childrenAllowed(new HTMLPurifier_ChildDef_List),
            'var'        => HtmlElement::make('var')->inlineContent(),
            'video'      => HtmlElement::make('video')->blockContent(),
            'wbr'        => HtmlElement::make('wbr')->inlineContent(),
        ];
    }

    private static function compileElement(HtmlElement $element): string
    {
        $attributes = Collection::make($element->getAttributes())->map->getAttribute()->implode(',');

        $result = $element->getElement();

        if ($attributes) {
            $result .= "[{$attributes}]";
        }

        return $result;
    }

    private function setAllowedElements(): self
    {
        $elements = Collection::make($this->allowedElements)
            ->map(function (HtmlElement $element) {
                return static::compileElement($element);
            })
            ->values()
            ->implode(',');

        $this->config->set('HTML.Allowed', $elements);

        return $this;
    }

    public function create(): HTMLPurifier_Config
    {
        $this->setAllowedElements();

        $htmlDefinition = $this->config->maybeGetRawHTMLDefinition(true);

        if (!$htmlDefinition) {
            return $this->config;
        }

        $this->htmlDefinition = $htmlDefinition;

        Collection::make($this->allowedElements)->each(function (HtmlElement $element) {
            $this->htmlDefinition->addElement(
                $element->getElement(),
                $element->getContentSet(),
                $element->getAllowedChildren(),
                $element->getAttributeCollection(),
                $element->_getAttributes(),
            );

            Collection::make($element->getAttributes())->each(function (HtmlAttribute $attribute) use ($element) {
                $this->htmlDefinition->addAttribute(
                    $element->getElement(),
                    $attribute->getAttribute(),
                    $attribute->getValidValues()
                );
            });
        });

        return $this->config;
    }

    public function allowElement($element, callable $withElement = null): self
    {
        $htmlElement = $element instanceof HtmlElement
            ? $element
            : new HtmlElement($element);

        if ($withElement) {
            $withElement($htmlElement);
        }

        $this->allowedElements[$htmlElement->getElement()] = $htmlElement;

        return $this;
    }

    public function loadArray(array $config = []): self
    {
        $this->config->loadArray($config);

        return $this;
    }
}
