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
        $this->expectExceptionMessage('Action not done.');

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
        $result = wrapper()
            ->abortIf(500)
            ->execute(false);

        $this->assertFalse($result);

        $this->expectException(HttpException::class);

        wrapper()
            ->abortIf(500)
            ->execute(true);
    }

    public function testAbortUnless(): void
    {
        $result = wrapper()
            ->abortUnless(500)
            ->execute(true);

        $this->assertTrue($result);

        $this->expectException(HttpException::class);

        wrapper()
            ->abortUnless(500)
            ->execute(false);
    }

    public function testAbortInternalServerErrorUnless(): void
    {
        $result = wrapper()
            ->abortInternalServerErrorUnless()
            ->execute(true);

        $this->assertTrue($result);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Something went wrong.');

        wrapper()
            ->abortInternalServerErrorUnless()
            ->execute(false);
    }
}