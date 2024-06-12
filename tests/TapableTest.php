<?php

namespace R3bzya\ActionWrapper\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TapableTest extends TestCase
{
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

    #[DataProvider('tapUnlessData')]
    public function testTapUnless(mixed $condition, int $assertionsCount): void
    {
        $result = wrapper()
            ->tapUnless($condition, fn(int $value) => $this->addToAssertionCount(1))
            ->execute(true);

        $this->assertTrue($result);
        $this->assertEquals($assertionsCount, $this->numberOfAssertionsPerformed());
    }

    public static function tapUnlessData(): array
    {
        return [
            [
                false,
                1,
            ],
            [
                true,
                0,
            ],
        ];
    }
}