<?php

namespace R3bzya\ActionWrapper\Tests\Concerns;

use R3bzya\ActionWrapper\Exceptions\NotDoneException;
use R3bzya\ActionWrapper\Tests\TestCase;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionableTest extends TestCase
{
    public function testThrowWhen(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error!');

        wrapper()
            ->throwIf(RuntimeException::class, 'Error!')
            ->execute(true);
    }

    public function testThrowUnless(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error!');

        wrapper()
            ->throwUnless('Error!')
            ->execute(false);
    }

    public function testThrowIfNotDone(): void
    {
        $this->expectException(NotDoneException::class);

        wrapper()
            ->throwIfNotDone(new NotDoneException)
            ->execute(false);
    }

    public function testCatch(): void
    {
        $this->assertFalse(
            wrapper()
                ->catch(false)
                ->execute(fn() => throw new RuntimeException),
        );

        $this->assertFalse(
            wrapper()
                ->catch(fn() => false)
                ->execute(fn() => throw new RuntimeException),
        );

        $this->assertFalse(
            wrapper()
                ->catch(function (Throwable $e) {
                    $this->assertInstanceOf(RuntimeException::class, $e);

                    return false;
                })
                ->execute(fn() => throw new RuntimeException),
        );
    }

    public function testThrowableInsteadOfThrow(): void
    {
        $this->assertInstanceOf(
            RuntimeException::class,
            wrapper()
                ->throwableInsteadOfThrow()
                ->execute(fn() => throw new RuntimeException),
        );
    }

    public function testFalseInsteadOfThrowable(): void
    {
        $this->assertFalse(
            wrapper()
                ->falseInsteadOfThrowable()
                ->execute(fn() => throw new RuntimeException),
        );
    }

    public function testAbortIf(): void
    {
        $this->expectException(HttpException::class);

        wrapper()
            ->abortIf(500)
            ->execute(true);
    }

    public function testAbortUnless(): void
    {
        $this->expectException(HttpException::class);

        wrapper()
            ->abortUnless(500)
            ->execute(false);
    }
}