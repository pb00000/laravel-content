<?php

namespace ProtoneMedia\LaravelContent\Tests\Sanitizers;

use ProtoneMedia\LaravelContent\Sanitizers\HtmlElement;
use ProtoneMedia\LaravelContent\Sanitizers\HtmlPurifier;
use ProtoneMedia\LaravelContent\Sanitizers\HtmlPurifierConfigFactory;
use ProtoneMedia\LaravelContent\Tests\TestCase;

class HtmlPurifierTest extends TestCase
{
    private function purifier(): HtmlPurifier
    {
        return app(HtmlPurifier::class);
    }

    /** @test */
    public function it_loads_the_default_config()
    {
        $cachePath = $this->purifier()->getInstance()->config->get('Cache.SerializerPath');

        $this->assertStringContainsString(storage_path(), $cachePath);
    }

    /** @test */
    public function it_can_allow_additional_elements()
    {
        $purifier = $this->purifier();

        $this->assertEquals(
            'value<p>value</p>',
            $purifier->execute('<custom-embed>value</custom-embed><p>value</p>')
        );

        $purifier->updateConfig(function (HtmlPurifierConfigFactory $config) {
            $config->allowElement('custom-embed');
        });

        $this->assertEquals(
            '<custom-embed>value</custom-embed><p>value</p>',
            $purifier->execute('<custom-embed>value</custom-embed><p>value</p>')
        );
    }

    /** @test */
    public function it_can_allow_additional_elements_with_attributes()
    {
        $purifier = $this->purifier();

        $purifier->updateConfig(function (HtmlPurifierConfigFactory $config) {
            $config->allowElement('custom-embed');
        });

        $this->assertEquals(
            '<custom-embed>value</custom-embed>',
            $purifier->execute('<custom-embed src="https://protone.media">value</custom-embed>')
        );

        $purifier->updateConfig(function ($config) {
            $config->allowElement('custom-embed', function (HtmlElement $element) {
                $element->allowAttribute('src');
            });
        });

        $this->assertEquals(
            '<custom-embed src="https://protone.media">value</custom-embed>',
            $purifier->execute('<custom-embed src="https://protone.media">value</custom-embed>')
        );
    }
}
