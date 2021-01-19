<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

use HTMLPurifier_Config;
use Illuminate\Support\Collection;

class HtmlPurifierConfigFactory
{
    private $config;

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
            'a'          => HtmlElement::make('a'),
            'address'    => HtmlElement::make('address'),
            'article'    => HtmlElement::make('article'),
            'aside'      => HtmlElement::make('aside'),
            'b'          => HtmlElement::make('b'),
            'blockquote' => HtmlElement::make('blockquote'),
            'br'         => HtmlElement::make('br'),
            'del'        => HtmlElement::make('del'),
            'div'        => HtmlElement::make('div'),
            'em'         => HtmlElement::make('em'),
            'figcaption' => HtmlElement::make('figcaption'),
            'figure'     => HtmlElement::make('figure'),
            'footer'     => HtmlElement::make('footer'),
            'h1'         => HtmlElement::make('h1'),
            'h2'         => HtmlElement::make('h2'),
            'h3'         => HtmlElement::make('h3'),
            'h4'         => HtmlElement::make('h4'),
            'h5'         => HtmlElement::make('h5'),
            'h6'         => HtmlElement::make('h6'),
            'header'     => HtmlElement::make('header'),
            'hgroup'     => HtmlElement::make('hgroup'),
            'i'          => HtmlElement::make('i'),
            'iframe'     => HtmlElement::make('iframe'),
            'img[src]'   => HtmlElement::make('img')->allowAttribute('src'),
            'ins'        => HtmlElement::make('ins'),
            'li'         => HtmlElement::make('li'),
            'mark'       => HtmlElement::make('mark'),
            'nav'        => HtmlElement::make('nav'),
            'ol'         => HtmlElement::make('ol'),
            'p'          => HtmlElement::make('p'),
            's'          => HtmlElement::make('s'),
            's'          => HtmlElement::make('s'),
            'section'    => HtmlElement::make('section'),
            'source'     => HtmlElement::make('source'),
            'span'       => HtmlElement::make('span'),
            'strong'     => HtmlElement::make('strong'),
            'sub'        => HtmlElement::make('sub'),
            'sup'        => HtmlElement::make('sup'),
            'table'      => HtmlElement::make('table'),
            'td'         => HtmlElement::make('td'),
            'th'         => HtmlElement::make('th'),
            'tr'         => HtmlElement::make('tr'),
            'u'          => HtmlElement::make('u'),
            'ul'         => HtmlElement::make('ul'),
            'var'        => HtmlElement::make('var'),
            'video'      => HtmlElement::make('video'),
            'wbr'        => HtmlElement::make('wbr'),
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

    private function getAllElements(): array
    {
        return $this->allowedElements;
    }

    public function create(): HTMLPurifier_Config
    {
        $this->setAllowedElements();

        $HTMLDefinition = $this->config->maybeGetRawHTMLDefinition(true);

        if (!$HTMLDefinition) {
            return $this->config;
        }

        foreach ($this->getAllElements() as $element) {
            $HTMLDefinition->addElement(
                $element->getElement(),
                $element->_getType(),
                $element->_getContents(),
                $element->_getAttrCollections(),
                $element->_getAttributes(),
            );

            foreach ($element->getAttributes() as $attribute) {
                $HTMLDefinition->addAttribute($element->getElement(), $attribute->getAttribute(), $attribute->_getDef());
            }
        }

        return $this->config;
    }

    public function allowElement(string $element, callable $withElement = null): self
    {
        $htmlElement = new HtmlElement($element);

        if ($withElement) {
            $withElement($htmlElement);
        }

        $this->allowedElements[$element] = $htmlElement;

        return $this;
    }

    public function loadArray(array $config = []): self
    {
        $this->config->loadArray($config);

        return $this;
    }
}
