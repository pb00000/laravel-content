<?php

namespace ProtoneMedia\LaravelContent\Tests\Pages;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ProtoneMedia\LaravelContent\Fields\Image;
use ProtoneMedia\LaravelContent\Fields\Text;
use ProtoneMedia\LaravelContent\Pages\Page;
use ProtoneMedia\LaravelContent\Tests\Post;
use ProtoneMedia\LaravelContent\Tests\TestCase;

class TitleAndHeader extends Page
{
    public function fields(): array
    {
        return [
            'title'  => new Text,
            'header' => new Image,
        ];
    }
}

class NestedData extends Page
{
    public function fields(): array
    {
        return [
            'title' => [
                'main' => new Text,
                'sub'  => [
                    'first'  => new Text,
                    'second' => new Text,
                ],
            ],
        ];
    }
}

class PageTest extends TestCase
{
    /** @test */
    public function it_can_populate_the_page_from_a_request()
    {
        $request = Request::createFromGlobals()->replace([
            'title' => 'New Title',
        ]);

        $request->files->set(
            'header',
            UploadedFile::fake()->image('header.jpg')
        );

        $post = TitleAndHeader::loadFromRequest($request)
            ->saveAsJson(new Post, 'title');

        $content = json_decode($post->title, true);

        $this->assertEquals('New Title', $content['title']);
        $this->assertIsArray($content['header']);
    }

    /** @test */
    public function it_can_handle_nested_data()
    {
        $request = Request::createFromGlobals()->replace([
            'title' => [
                'main' => 'Main Title',
                'sub'  => [
                    'first'  => 'Sub 1 Title',
                    'second' => 'Sub 2 Title',
                ],
            ],
        ]);

        $post = NestedData::loadFromRequest($request)
            ->saveAsJson(new Post, 'title');

        $content = json_decode($post->title, true);

        $this->assertEquals('Main Title', $content['title']['main']);
        $this->assertEquals('Sub 1 Title', $content['title']['sub']['first']);
        $this->assertEquals('Sub 2 Title', $content['title']['sub']['second']);
    }

    /** @test */
    public function it_can_save_the_content_in_a_model()
    {
        $request = Request::createFromGlobals()->replace([
            'title' => 'New Title',
        ]);

        $request->files->set(
            'header',
            UploadedFile::fake()->image('header.jpg')
        );

        $post = TitleAndHeader::loadFromRequest($request)
            ->saveToModel(new Post);

        $this->assertEquals(json_encode('New Title'), $post->title);
        $this->assertIsArray(json_decode($post->header, true));
    }
}
