<?php

namespace R3bzya\ActionWrapper\Tests;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use R3bzya\ActionWrapper\Exceptions\NotDoneException;
use RuntimeException;
use Throwable;

class ActionWrapperTest extends TestCase
{
    public function testThrough(): void
    {
        $result = wrapper()
            ->through(fn(array $arguments, Closure $next) => $next($arguments) + 1)
            ->execute(1);

        $this->assertEquals(2, $result);
    }

    public function testMultiplyThrough(): void
    {
        $result = wrapper()
            ->through(fn(array $arguments, Closure $next) => $next($arguments) - 1)
            ->through(fn(array $arguments, Closure $next) => 2 * $next($arguments))
            ->after(fn(int $value) => $value + 3)
            ->before(fn(int $value) => [$value + 2])
            ->tap(fn(int $value) => $value + 1) // no effect
            ->execute(1);

        $this->assertEquals(11, $result);
    }

    public function testInvokableDecorator(): void
    {
        $result = wrapper()
            ->through(new readonly class(1) {
                public function __construct(private int $value) {}

                public function __invoke(array $arguments, Closure $next): int
                {
                    return $next($arguments) + $this->value;
                }
            })
            ->execute(1);

        $this->assertEquals(2, $result);
    }

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

    #[DataProvider('beforeData')]
    public function testBefore(callable $decorator, array $value, mixed $expected): void
    {
        $result = wrapper()
            ->before($decorator)
            ->execute(...$value);

        $this->assertEquals($expected, $result);
    }

    public static function beforeData(): array
    {
        return [
            [
                fn(...$arguments) => [$arguments[0] + 1],
                [1],
                2,
            ],
            [
                fn(...$arguments) => ['value' => $arguments['value'] + 1],
                ['value' => 1],
                2,
            ],
            [
                fn(int $value) => [$value + 1],
                [1],
                2,
            ],
            [
                fn(int $value) => ['value' => $value + 1],
                [1],
                2,
            ],
            [
                fn(int $value, int $foo = null) => ['value' => $value + 1],
                ['foo' => 2, 'value' => 1],
                2,
            ],
            [
                fn() => false,
                [true],
                false,
            ],
        ];
    }

    public function testAfter(): void
    {
        $result = wrapper()
            ->after(fn(int $value) => $value + 1)
            ->execute(1);

        $this->assertEquals(2, $result);
    }

    public function testFluentAction(): void
    {
        $this->assertTrue(wrapper()->execute(fn() => true));
        $this->assertTrue(wrapper()->execute(true));
        $this->assertTrue(wrapper()->execute(fn(bool $value) => $value, true));
    }

    public function testTap(): void
    {
        $result = wrapper()
            ->tap(fn(int $value) => $value + 1)
            ->execute(1);

        $this->assertEquals(1, $result);
    }

    #[DataProvider('tapWhenData')]
    public function testTapWhen(mixed $condition, int $assertionsCount): void
    {
        $result = wrapper()
            ->tapWhen($condition, fn(int $value) => $this->addToAssertionCount(1))
            ->execute(true);

        $this->assertTrue($result);
        $this->assertEquals($assertionsCount, $this->numberOfAssertionsPerformed());
    }

    public static function tapWhenData(): array
    {
        return [
            [
                true,
                1,
            ],
            [
                false,
                0,
            ],
        ];
    }

    #[DataProvider('whenData')]
    public function testWhen(mixed $condition, callable $callable, mixed $expected): void
    {
        $result = wrapper()
            ->when($condition, $callable)
            ->execute('unexpected');

        $this->assertEquals($expected, $result);
    }

    public static function whenData(): array
    {
        return [
            [
                true,
                fn() => 'new_string',
                'new_string',
            ],
            [
                fn(string $result) => $result == 'unexpected',
                fn(string $result) => 'new_string',
                'new_string',
            ],
            [
                fn() => true,
                fn() => 'new_string',
                'new_string',
            ],
            [
                false,
                fn() => 'new_string',
                'unexpected',
            ],
            [
                fn(string $result) => $result !== 'unexpected',
                fn(string $result) => 'new_string',
                'unexpected',
            ],
            [
                fn() => false,
                fn() => 'new_string',
                'unexpected',
            ],
        ];
    }

    #[DataProvider('unlessData')]
    public function testUnless(mixed $condition, callable $callable, mixed $expected): void
    {
        $result = wrapper()
            ->unless($condition, $callable)
            ->execute('expected');

        $this->assertEquals($expected, $result);
    }

    public static function unlessData(): array
    {
        return [
            [
                false,
                fn() => 'new_string',
                'new_string',
            ],
            [
                fn(string $result) => $result != 'expected',
                fn(string $result) => 'new_string',
                'new_string',
            ],
            [
                fn() => false,
                fn() => 'new_string',
                'new_string',
            ],
            [
                true,
                fn() => 'new_string',
                'expected',
            ],
            [
                fn(string $result) => $result !== 'unexpected',
                fn(string $result) => 'new_string',
                'expected',
            ],
            [
                fn() => true,
                fn() => 'new_string',
                'expected',
            ],
        ];
    }

    public function testTry(): void
    {
        $this->assertFalse(
            wrapper()
                ->try(false)
                ->execute(fn() => throw new RuntimeException)
        );

        $this->assertFalse(
            wrapper()
                ->try(fn() => false)
                ->execute(fn() => throw new RuntimeException)
        );

        $this->assertFalse(
            wrapper()
                ->try(function (Throwable $e) {
                    $this->assertInstanceOf(RuntimeException::class, $e);

                    return false;
                })
                ->execute(fn() => throw new RuntimeException)
        );
    }

    public function testCatch(): void
    {
        $this->assertInstanceOf(
            RuntimeException::class,
            wrapper()
                ->catch()
                ->execute(fn() => throw new RuntimeException)
        );
    }

    public function testSafe(): void
    {
        $this->assertFalse(
            wrapper()
                ->safe()
                ->execute(fn() => throw new RuntimeException)
        );
    }
}