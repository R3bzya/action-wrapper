<?php

namespace R3bzya\ActionWrapper\Tests\Unit;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use R3bzya\ActionWrapper\Support\Payloads\Payload;
use R3bzya\ActionWrapper\Tests\Feature\TestCase;
use RuntimeException;

class HasPayloadTest extends TestCase
{
    public function testPayload(): void
    {
        wrapper()
            ->payload(fn(Payload $payload) => $this->addToAssertionCount(1))
            ->execute(true);

        $this->assertEquals(1, $this->numberOfAssertionsPerformed());
    }

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

    public function testHasException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Hello world!');

        wrapper()
            ->payload(function (Payload $payload) {
                $this->assertInstanceOf(RuntimeException::class, $payload->getException());
            })
            ->execute(fn() => throw new RuntimeException('Hello world!'));
    }

    #[DataProvider('payloadWhenData')]
    public function testPayloadWhen(int $expectedAssertionsCount, mixed $value): void
    {
        wrapper()
            ->payloadWhen(function () {
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
            ->payloadUnless(function () {
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