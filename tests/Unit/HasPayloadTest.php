<?php

namespace R3bzya\ActionWrapper\Tests\Unit;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use R3bzya\ActionWrapper\Support\Payloads\Payload;
use RuntimeException;

class HasPayloadTest extends TestCase
{
    public function testResult(): void
    {
        $expected = Str::random(4);

        $result = wrapper()
            ->payload(function (Payload $payload) use ($expected) {
                $this->assertSame($expected, $payload->getResult());
            })
            ->execute($expected);

        $this->assertSame($expected, $result);
    }

    public function testArguments(): void
    {
        $expected = Str::random(4);

        $result = wrapper()
            ->payload(function (Payload $payload) use ($expected) {
                $this->assertSame([$expected], $payload->getArguments());
            })
            ->execute($expected);

        $this->assertSame($expected, $result);
    }

    #[DataProvider('passesData')]
    public function testPasses(bool $value): void
    {
        wrapper()
            ->payload(function (Payload $payload) {
                $this->assertTrue($payload->passes());
            })
            ->execute($value);
    }

    public static function passesData(): array
    {
        return [
            [true],
            [false],
        ];
    }

    #[DataProvider('failsData')]
    public function testFails(bool $value): void
    {
        wrapper()
            ->payload(function (Payload $payload) {
                $this->assertFalse($payload->fails());
            })
            ->execute($value);
    }

    public static function failsData(): array
    {
        return [
            [true],
            [false],
        ];
    }

    public function testHasException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Hello world!');

        wrapper()
            ->payload(function (Payload $payload) {
                $this->assertTrue($payload->fails());
                $this->assertTrue($payload->hasException());
                $this->assertInstanceOf(RuntimeException::class, $payload->getException());
            })
            ->execute(fn() => throw new RuntimeException('Hello world!'));
    }

    public function testHasNotException(): void
    {
        wrapper()
            ->payload(function (Payload $payload) {
                $this->assertTrue($payload->passes());
                $this->assertTrue($payload->hasNotException());
            })
            ->execute(true);
    }

    #[DataProvider('hasNotDoneData')]
    public function testHasNotDone(bool $expected, mixed $value): void
    {
        try {
            wrapper()
                ->payload(function (Payload $payload) use ($expected) {
                    $this->assertSame($expected, $payload->isNotCompleted());
                })
                ->execute($value);
        } catch (RuntimeException $e) {
            $this->assertSame('Hello world!', $e->getMessage());
        }
    }

    public static function hasNotDoneData(): array
    {
        return [
            [false, true],
            [true, false],
            [true, fn() => throw new RuntimeException('Hello world!')],
        ];
    }

    #[DataProvider('payloadWhenData')]
    public function testPayloadWhen(int $expectedAssertionsCount, mixed $value): void
    {
        wrapper()
            ->payloadWhen(function (Payload $payload) {
                $this->addToAssertionCount(1);
            }, $value)
            ->execute(true);

        $this->assertSame($expectedAssertionsCount, $this->numberOfAssertionsPerformed());
    }

    public static function payloadWhenData(): array
    {
        return [
            [1, true],
            [0, false],
            [1, fn(Payload $payload) => $payload->passes()],
            [0, fn(Payload $payload) => $payload->fails()],
        ];
    }

    #[DataProvider('payloadUnlessData')]
    public function testPayloadUnless(int $expectedAssertionsCount, mixed $value): void
    {
        wrapper()
            ->payloadUnless(function (Payload $payload) {
                $this->addToAssertionCount(1);
            }, $value)
            ->execute(true);

        $this->assertSame($expectedAssertionsCount, $this->numberOfAssertionsPerformed());
    }

    public static function payloadUnlessData(): array
    {
        return [
            [0, true],
            [1, false],
            [0, fn(Payload $payload) => $payload->passes()],
            [1, fn(Payload $payload) => $payload->fails()],
        ];
    }
}