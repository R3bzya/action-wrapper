<?php

namespace R3bzya\ActionWrapper\Tests\Support\Traits\Simples;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use R3bzya\ActionWrapper\Support\FluentAction;

class HasActionWrapperTest extends TestCase
{
    protected function tearDown(): void
    {
        FluentAction::flushMacros();
    }

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

    public function testMacro(): void
    {
        FluentAction::macro('plus', function (int $value) {
            /** @var FluentAction $this */
            return $this->after(fn(int $result) => $result + $value);
        });

        $this->assertSame(10, wrapper()->plus(5)->execute(5));
    }

    public function testFlushPipes(): void
    {
        $wrapper = wrapper()
            ->after(fn(int $value) => $value);

        $this->assertCount(1, $wrapper->pipes());

        $wrapper->flushPipes();

        $this->assertCount(0, $wrapper->pipes());
    }
}
