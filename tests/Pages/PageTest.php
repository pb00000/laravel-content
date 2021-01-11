<?php

namespace ProtoneMedia\LaravelContent\Tests\Pages;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ProtoneMedia\LaravelContent\Fields\Image;
use ProtoneMedia\LaravelContent\Fields\Text;
use ProtoneMedia\LaravelContent\Pages\Page;
use ProtoneMedia\LaravelContent\Tests\Post;
use ProtoneMedia\LaravelContent\Tests\TestCase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
            'header' => new Image,
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

        $content           = json_decode($post->title, true);
        $content['header'] = json_decode($content['header'], true);

        $this->assertEquals('New Title', $content['title']);
        $this->assertIsArray($content['header']);
    }

    /** @test */
    public function it_can_populate_the_page_from_a_model_field()
    {
        $request = Request::createFromGlobals()->replace([
            'title' => 'New Title',
        ]);

        $request->files->set(
            'header',
            UploadedFile::fake()->image('header.jpg')
        );

        $post = TitleAndHeader::loadFromRequest($request)->saveAsJson(new Post, 'title');

        $page = TitleAndHeader::loadFromModel($post, 'title');

        $this->assertEquals('New Title', $page->title->getValue());
        $this->assertEquals('New Title', $page['title']->getValue());
        $this->assertInstanceOf(Media::class, $page->header->getMedia());
        $this->assertStringContainsString('header.jpg', $page->header->toHtml());
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

        $this->assertEquals('New Title', $post->title);
        $this->assertIsArray(json_decode($post->header, true));
    }

    /** @test */
    public function it_can_populate_the_page_from_a_model_with_nested_data()
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

        $request->files->set(
            'header',
            UploadedFile::fake()->image('header.jpg')
        );

        $post = NestedData::loadFromRequest($request)->saveToModel(new Post);

        $page = NestedData::loadFromModel($post);

        $this->assertEquals('Main Title', $page['title.main']->getValue());
        $this->assertEquals('Main Title', $page['title']['main']->getValue());
        $this->assertEquals('Sub 1 Title', $page['title.sub.first']->getValue());
        $this->assertEquals('Sub 2 Title', $page['title.sub.second']->getValue());
        $this->assertEquals('Sub 2 Title', $page['title']['sub']['second']->getValue());
        $this->assertInstanceOf(Image::class, $page['header']);
    }
}
