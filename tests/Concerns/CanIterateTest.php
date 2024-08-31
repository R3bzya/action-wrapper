<?php

namespace R3bzya\ActionWrapper\Tests\Concerns;

use PHPUnit\Framework\Attributes\DataProvider;
use R3bzya\ActionWrapper\Tests\TestCase;

class CanIterateTest extends TestCase
{
    #[DataProvider('eachData')]
    public function testEach(mixed $data, array $excepted): void
    {
        $this->assertSame($excepted, wrapper()->each($data)->all());
    }

    public static function eachData(): array
    {
        return [
            [
                'Hello world!',
                ['Hello world!'],
            ],
            [
                fn() => ['value' => 'Hello world!'],
                ['Hello world!'],
            ],
            [
                ['Hello world!'],
                ['Hello world!']
            ],
            [
                [1, 2, 3, 4, 5],
                [1, 2, 3, 4, 5],
            ],
        ];
    }
}