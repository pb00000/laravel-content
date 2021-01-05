<?php

namespace ProtoneMedia\LaravelContent\Tests\Requests;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ProtoneMedia\LaravelContent\Fields\Image;
use ProtoneMedia\LaravelContent\Media\MediaLibraryRepository;
use ProtoneMedia\LaravelContent\Tests\Post as PostModel;
use ProtoneMedia\LaravelContent\Tests\TestCase;

class ImageModel extends PostModel
{
    protected $casts = [
        'title' => Image::class,
    ];
}

class ImageUploadTest extends TestCase
{
    private function setupRoute()
    {
        $this->app['router']->group(['middleware' => 'web'], function ($router) {
            $router->post('/upload', function (Request $request) {
                return ImageModel::create([
                    'title' => Image::fromRequest($request)->get('logo_image'),
                ]);
            });

            $router->post('/validate', function (Request $request) {
                $request->validate([
                    'logo_image' => Image::fromRequest()->get('logo_image')->getRules(),
                ]);

                return [];
            });
        });

        return $this;
    }

    /** @test */
    public function it_can_save_an_uploaded_file()
    {
        $this->setupRoute()
            ->post('/upload', [
                'logo_image' => UploadedFile::fake()->image('logo.png'),
            ])
            ->assertCreated()
            ->json();

        $model = ImageModel::first();
        $field = $model->title;

        $this->assertTrue($field->getMedia()->model->is($model));
    }

    /** @test */
    public function it_can_submit_an_existing_media_model()
    {
        $repository = app(MediaLibraryRepository::class);

        $mediaModel = $repository->storeTemporarily(
            UploadedFile::fake()->image('logo.png')
        );

        $media = new Image($mediaModel, $repository);

        $this->setupRoute()
            ->post('/upload', [
                'logo_image' => $media->toArray(),
            ])
            ->assertCreated()
            ->json();

        $model = ImageModel::first();
        $field = $model->title;

        $this->assertTrue($field->getMedia()->model->is($model));
    }

    /** @test */
    public function it_can_validate_an_uploaded_file()
    {
        $this->setupRoute()
            ->postJson('/validate', [
                'logo_image' => UploadedFile::fake()->image('logo.mp3'),
            ])
            ->assertJsonValidationErrors(['logo_image']);
    }

    /** @test */
    public function it_can_validate_an_existing_media_model()
    {
        $repository = app(MediaLibraryRepository::class);

        $mediaModel = $repository->storeTemporarily(
            UploadedFile::fake()->image('logo.png')
        );

        $media = new Image($mediaModel, $repository);

        $this->setupRoute()
            ->post('/validate', [
                'logo_image' => $media->toArray(),
            ])
            ->assertOk();
    }
}
