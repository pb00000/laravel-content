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

        $post = TitleAndHeader::fromRequest($request)
            ->saveAsJson(new Post, 'title');
    }
}
