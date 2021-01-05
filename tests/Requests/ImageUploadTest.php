<?php

namespace ProtoneMedia\LaravelContent\Tests\Requests;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
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
                    'title' => Image::fromRequest('logo_image', $request),
                ]);
            });

            $router->post('/validate/{size?}', function (Request $request, $size = 10) {
                Image::prepareRequestForValidation('logo_image', $request);

                $request->validate([
                    'logo_image' => Image::empty()
                        ->addToRules(Rule::dimensions()->minWidth($size)->minHeight($size))
                        ->getRules(),
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
            ->assertCreated();

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
    public function it_can_validate_an_uploaded_file_with_a_size_check()
    {
        $this->setupRoute()
            ->postJson('/validate/100', [
                'logo_image' => UploadedFile::fake()->image('logo.png', 50, 50),
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

    /** @test */
    public function it_can_handle_an_unexisting_media_model()
    {
        $repository = app(MediaLibraryRepository::class);

        $mediaModel = $repository->storeTemporarily(
            UploadedFile::fake()->image('logo.png')
        );

        $media = new Image($mediaModel, $repository);

        $data = $media->toArray();

        $mediaModel->delete();

        $this->setupRoute()
            ->postJson('/validate', [
                'logo_image' => $data,
            ])
            ->assertJsonValidationErrors(['logo_image']);
    }
}
