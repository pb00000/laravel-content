<?php

namespace ProtoneMedia\LaravelContent\Tests\Requests;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ProtoneMedia\LaravelContent\Fields\Image;
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
        });

        return $this;
    }

    /** @test */
    public function it_can_parse_the_request()
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
}
