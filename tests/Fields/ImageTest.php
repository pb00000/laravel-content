<?php

namespace ProtoneMedia\LaravelContent\Tests\Fields;

use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelContent\Fields\Image;
use ProtoneMedia\LaravelContent\Media\MediaLibraryRepository;
use ProtoneMedia\LaravelContent\Tests\Post as PostModel;
use ProtoneMedia\LaravelContent\Tests\TestCase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends PostModel
{
    protected $casts = [
        'title' => Image::class,
    ];
}

class ImageTest extends TestCase
{
    /** @test */
    public function it_can_return_the_model()
    {
        $media = new Media;
        $image = new Image($media);

        $this->assertEquals($media, $image->getMedia());
    }

    /** @test */
    public function it_can_store_a_text_field_to_the_database_and_retrieve_it()
    {
        $media = $this->createMediaWithSingleImage();

        $post = Post::create([
            'title' => new Image($media),
        ]);

        $this->assertEquals(json_encode([
            'key'        => $media->getKey(),
            'repository' => MediaLibraryRepository::class,
        ]), DB::table('posts')->value('title'));

        $title = $post->fresh()->title;

        $this->assertInstanceOf(Image::class, $title);
        $this->assertTrue($title->getMedia()->is($media));
    }

    /** @test */
    public function it_can_return_the_html()
    {
        $media = $this->createMediaWithSingleImage();

        $post = Post::create([
            'title' => new Image($media),
        ]);

        $this->assertStringContainsString('<img', $post->title->toHtml());
    }

    /** @test */
    public function it_forwards_calls()
    {
        $media = $this->createMediaWithSingleImage();

        $post = Post::create([
            'title' => new Image($media),
        ]);

        $this->assertStringContainsString('<img', $post->title->img());
    }
}
