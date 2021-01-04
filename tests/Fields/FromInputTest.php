<?php

namespace ProtoneMedia\LaravelContent\Tests\Fields;

use ProtoneMedia\LaravelContent\Fields\FromInput;
use ProtoneMedia\LaravelContent\Tests\TestCase;

class ClassWithTwoArguments
{
    public $two;
    public $four;

    public function __construct($two, $four)
    {
        $this->two  = $two;
        $this->four = $four;
    }
}

class FromInputTest extends TestCase
{
    /** @test */
    public function it_can_handle_multiple_arguments()
    {
        $fromInput = new FromInput(ClassWithTwoArguments::class, 1, 2);

        $fromInput->withMiddleware([
            new class {
                public function execute($one, $two)
                {
                    return [$one * 2, $two * 2];
                }
            },
        ]);

        $object = $fromInput->resolve();

        $this->assertEquals(2, $object->two);
        $this->assertEquals(4, $object->four);
    }

    /** @test */
    public function it_can_a_callable_middleware()
    {
        $fromInput = new FromInput(ClassWithTwoArguments::class, 1, 2);

        $fromInput->withMiddleware([
            function ($one, $two) {
                return [$one * 2, $two * 2];
            },
        ]);

        $object = $fromInput->resolve();

        $this->assertEquals(2, $object->two);
        $this->assertEquals(4, $object->four);
    }
}
