<?php

namespace R3bzya\ActionWrapper\Tests;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use R3bzya\ActionWrapper\Actions\FluentAction;
use R3bzya\ActionWrapper\Exceptions\NotDoneException;
use RuntimeException;

class ActionWrapperTest extends TestCase
{
    public function testThrough(): void
    {
        $result = (new FluentAction)
            ->through(function (array $arguments, Closure $next) {
                return $next($arguments) + 1;
            })
            ->execute(1);

        $this->assertEquals(2, $result);
    }

    public function testMultiplyThrough(): void
    {
        $result = (new FluentAction)
            ->through(function (array $arguments, Closure $next) {
                return $next($arguments) - 1;
            })
            ->through(function (array $arguments, Closure $next) {
                return 2 * $next($arguments);
            })
            ->after(function (int $value) {
                return $value + 3;
            })
            ->before(function (int $value) {
                return [$value + 2];
            })
            ->tap(function (int $value) {
                return $value + 1; // no effect
            })
            ->execute(1);

        $this->assertEquals(11, $result);
    }

    public function testInvokableDecorator(): void
    {
        $result = (new FluentAction)
            ->through(new class(1) {
                public function __construct(private readonly int $value) {}

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

        (new FluentAction)
            ->throwWhen(fn(bool $result) => $result == false)
            ->execute(false);
    }

    public function testThrowUnless(): void
    {
        $this->expectException(RuntimeException::class);

        (new FluentAction)
            ->throwUnless(fn(bool $result) => $result == true)
            ->execute(false);
    }

    public function throwIf(): void
    {
        $this->markTestSkipped();
    }

    public function throwIfNot(): void
    {
        $this->markTestSkipped();
    }

    #[DataProvider('throwIfNotDoneData')]
    public function testThrowIfNotDone(mixed $value, bool $strict): void
    {
        $this->expectException(NotDoneException::class);

        (new FluentAction)
            ->throwIfNotDone(new NotDoneException, $strict)
            ->execute($value);
    }

    public static function throwIfNotDoneData(): array
    {
        return [
            [
                false,
                true,
            ],
            [
                [],
                false,
            ],
        ];
    }

    #[DataProvider('beforeData')]
    public function testBefore(callable $decorator, array $value, mixed $expected): void
    {
        $result = (new FluentAction)
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
                2
            ],
            [
                fn(...$arguments) => ['value' => $arguments['value'] + 1],
                ['value' => 1],
                2
            ],
            [
                fn(int $value) => [$value + 1],
                [1],
                2
            ],
            [
                fn(int $value) => ['value' => $value + 1],
                [1],
                2
            ],
            [
                fn(int $value, int $foo = null) => ['value' => $value + 1],
                ['foo' => 2, 'value' => 1],
                2
            ],
            [
                fn() => false,
                [true],
                false
            ],
        ];
    }

    public function testAfter(): void
    {
        $result = (new FluentAction)
            ->after(function (int $value) {
                return $value + 1;
            })
            ->execute(1);

        $this->assertEquals(2, $result);
    }

    public function testFluentAction(): void
    {
        $this->assertTrue((new FluentAction)->execute(fn() => true));
        $this->assertFalse((new FluentAction)->execute(false));
        $this->assertTrue((new FluentAction)->execute(fn(bool $value) => $value, true));
    }

    public function testTap(): void
    {
        $result = (new FluentAction)
            ->tap(function (int $value) {
                return $value + 1;
            })
            ->execute(1);

        $this->assertEquals(1, $result);
    }

    #[DataProvider('whenData')]
    public function testWhen(mixed $condition, callable $callable, mixed $expected): void
    {
        $result = (new FluentAction)
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
                'new_string'
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
                'unexpected'
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
        $result = (new FluentAction)
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
                'new_string'
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
                'expected'
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
}