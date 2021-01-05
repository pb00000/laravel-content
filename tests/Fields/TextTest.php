<?php

namespace ProtoneMedia\LaravelContent\Tests\Fields;

use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelContent\Fields\Text;
use ProtoneMedia\LaravelContent\Tests\Post as PostModel;
use ProtoneMedia\LaravelContent\Tests\TestCase;

class Post extends PostModel
{
    protected $casts = [
        'title' => Text::class,
    ];
}

class TextTest extends TestCase
{
    /** @test */
    public function it_can_return_the_value_which_is_immutable()
    {
        $text = new Text('test');

        $this->assertEquals('test', $text->getValue());
    }

    /** @test */
    public function it_runs_the_default_middleware_when_parsing_input()
    {
        // trim
        $text = Text::fromInput('test ');
        $this->assertEquals('test', $text->getValue());

        // empty
        $text = Text::fromInput(' ');
        $this->assertEquals(null, $text->getValue());
    }

    /** @test */
    public function it_can_override_the_middleware()
    {
        $text = Text::fromInput('test ')->withMiddleware([]);
        $this->assertEquals('test ', $text->getValue());
    }

    /** @test */
    public function it_can_store_a_text_field_to_the_database_and_retrieve_it()
    {
        $post = Post::create([
            'title' => new Text('Title'),
        ]);

        $this->assertEquals('Title', DB::table('posts')->value('title'));

        $title = $post->fresh()->title;

        $this->assertInstanceOf(Text::class, $title);
        $this->assertEquals('Title', $title->getValue());
    }

    /** @test */
    public function it_can_overwrite_the_rules()
    {
        $this->assertEquals(['string'], (new Text)->getRules());
        $this->assertEquals(['max:5'], (new Text)->setRules(['max:5'])->getRules());
        $this->assertEquals(['max:5', 'min:3'], (new Text)->setRules(['max:5'])->mergeRules(['min:3'])->getRules());
        $this->assertEquals(['string', 'min:3'], (new Text)->mergeRules(['min:3'])->getRules());
    }
}
