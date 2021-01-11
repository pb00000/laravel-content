<?php

namespace ProtoneMedia\LaravelContent\Tests\Middleware;

use ProtoneMedia\LaravelContent\Middleware\MiddlewareHandler;
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

class MiddlewareHandlerTest extends TestCase
{
    /** @test */
    public function it_can_handle_multiple_arguments()
    {
        $handler = new MiddlewareHandler();

        $handler->withMiddleware([
            new class {
                public function execute($one, $two)
                {
                    return [$one * 2, $two * 2];
                }
            },
        ]);

        $object = new ClassWithTwoArguments(...$handler->execute(1, 2));

        $this->assertEquals(2, $object->two);
        $this->assertEquals(4, $object->four);
    }

    /** @test */
    public function it_can_a_callable_middleware()
    {
        $handler = new MiddlewareHandler();

        $handler->withMiddleware([
            function ($one, $two) {
                return [$one * 2, $two * 2];
            },
        ]);

        $object = new ClassWithTwoArguments(...$handler->execute(1, 2));

        $this->assertEquals(2, $object->two);
        $this->assertEquals(4, $object->four);
    }
}
