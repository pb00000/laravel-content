<?php declare(strict_types=1);

namespace ProtoneMedia\LaravelContent\Sanitizers;

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
        $this->config = HTMLPurifier_Config::createDefault();

        if (!is_null($config)) {
            $allConfig = $config->getAll();

            $allConfig['HTML']['DefinitionRev']++;

            $this->config->loadArray($allConfig);
        }

        $this->allowedElements = DefaultAllowedElements::get();
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
                'HTML.DefinitionRev'          => 1,
            ]);
        });
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

    public function create(): HTMLPurifier_Config
    {
        Collection::make($this->allowedElements)
            ->map(function (HtmlElement $element) {
                return static::compileElement($element);
            })
            ->tap(function (Collection $elements) {
                $this->config->set('HTML.Allowed', $elements->implode(','));
                $this->config->set('HTML.DefinitionID', md5(json_encode($this->allowedElements)));
            });

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
