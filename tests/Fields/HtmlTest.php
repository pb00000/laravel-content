<?php

namespace ProtoneMedia\LaravelContent\Tests\Fields;

use ProtoneMedia\LaravelContent\Fields\Html;
use ProtoneMedia\LaravelContent\Tests\TestCase;

class HtmlTest extends TestCase
{
    /** @test */
    public function it_sanitizes_the_html_but_not_the_value()
    {
        $text = new Html('<p>test</p><good>bye</good>');

        $this->assertStringContainsString('<good>', $text->getValue());
        $this->assertStringNotContainsString('<good>', $text->toHtml());
    }
}
