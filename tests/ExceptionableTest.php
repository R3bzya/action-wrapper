<?php

namespace R3bzya\ActionWrapper\Tests;

use PHPUnit\Framework\TestCase;
use R3bzya\ActionWrapper\Exceptions\NotDoneException;
use RuntimeException;
use Throwable;

class ExceptionableTest extends TestCase
{
    public function testThrowWhen(): void
    {
        $this->expectException(RuntimeException::class);

        wrapper()
            ->throwWhen(true)
            ->execute(false);
    }

    public function testThrowUnless(): void
    {
        $this->expectException(RuntimeException::class);

        wrapper()
            ->throwUnless(false)
            ->execute(false);
    }

    public function testThrowIfNotDone(): void
    {
        $this->expectException(NotDoneException::class);

        wrapper()
            ->throwIfNotDone(new NotDoneException)
            ->execute(false);
    }

    public function testTry(): void
    {
        $this->assertFalse(
            wrapper()
                ->try(false)
                ->execute(fn() => throw new RuntimeException),
        );

        $this->assertFalse(
            wrapper()
                ->try(fn() => false)
                ->execute(fn() => throw new RuntimeException),
        );

        $this->assertFalse(
            wrapper()
                ->try(function (Throwable $e) {
                    $this->assertInstanceOf(RuntimeException::class, $e);

                    return false;
                })
                ->execute(fn() => throw new RuntimeException),
        );
    }

    public function testCatch(): void
    {
        $this->assertInstanceOf(
            RuntimeException::class,
            wrapper()
                ->catch()
                ->execute(fn() => throw new RuntimeException),
        );
    }

    public function testSafe(): void
    {
        $this->assertFalse(
            wrapper()
                ->safe()
                ->execute(fn() => throw new RuntimeException),
        );
    }
}