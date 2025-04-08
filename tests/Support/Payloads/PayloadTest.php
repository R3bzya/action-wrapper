<?php

namespace R3bzya\ActionWrapper\Tests\Support\Payloads;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use R3bzya\ActionWrapper\Support\Payloads\Payload;
use RuntimeException;
use Throwable;

class PayloadTest extends TestCase
{
    #[DataProvider('getArgumentsData')]
    public function testGetArguments(array $arguments): void
    {
        $payload = (new Payload)
            ->setArguments($arguments);

        $this->assertEquals($arguments, $payload->getArguments());
    }

    public static function getArgumentsData(): array
    {
        return [
            [[]],
            [[1,2,3]]
        ];
    }

    public function testGetStartedAt(): void
    {
        $payload = new Payload;

        $this->assertNotNull($payload->getStartedAt());
        $this->assertInstanceOf(Carbon::class, $payload->getStartedAt());
    }

    public function testResult(): void
    {
        $payload = new Payload;

        $this->assertFalse($payload->hasResult());

        $payload->setResult($result = Str::random(4));

        $this->assertTrue($payload->hasResult());

        $this->assertSame($result, $payload->getResult());

        $payload->forgetResult();

        $this->assertFalse($payload->hasResult());
    }

    public function testResultIsNotSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The result does not exist in the payload.');

        (new Payload)->getResult();
    }

    public function testException(): void
    {
        $payload = new Payload;

        $this->assertFalse($payload->hasException());

        $payload->setException(new RuntimeException);

        $this->assertTrue($payload->hasException());

        $this->assertInstanceOf(RuntimeException::class, $payload->getException());

        $payload->forgetException();

        $this->assertFalse($payload->hasException());
    }

    public function testExceptionIsNotSet(): void
    {
        $this->assertNull((new Payload)->getException());
    }

    /**
     * @throws Throwable
     */
    public function testFireException(): void
    {
        $payload = new Payload;

        $this->assertInstanceOf(Payload::class, $payload->fireException());

        $payload->setException(new RuntimeException);

        $this->expectException(RuntimeException::class);

        $payload->fireException();
    }

    public function testPasses(): void
    {
        $payload = new Payload;

        $this->assertFalse($payload->passes());

        $payload->setResult(true);

        $this->assertTrue($payload->passes());

        $payload->setException(new RuntimeException);

        $this->assertFalse($payload->passes());
    }

    public function testCompletePayloadWithResult(): void
    {
        $payload = new Payload;
        $payload->setResult(false);
        $payload->complete();

        $this->assertTrue($payload->isCompleted());
        $this->assertNotNull($payload->getCompletedAt());
    }

    public function testCompletePayloadWithException(): void
    {
        $payload = new Payload;
        $payload->setException(new RuntimeException);
        $payload->complete();

        $this->assertTrue($payload->isCompleted());
        $this->assertNotNull($payload->getCompletedAt());
    }

    public function testCompleteMoreThanOnce(): void
    {
        $payload = new Payload;
        $payload->setResult(true);
        $payload->complete($completedAt = Carbon::now());

        $this->assertTrue($payload->isCompleted());
        $this->assertNotNull($payload->getCompletedAt());

        $payload->complete($notCompletedAt = Carbon::now()->addDays());

        $this->assertTrue($payload->getCompletedAt()->eq($completedAt));
        $this->assertFalse($payload->getCompletedAt()->eq($notCompletedAt));
    }

    public function testIncomplete(): void
    {
        $payload = new Payload;

        $payload->setResult(false);
        $payload->complete();

        $this->assertTrue($payload->isCompleted());

        $payload->incomplete();

        $this->assertFalse($payload->isCompleted());
    }

    public function testCycleTime(): void
    {
        $payload = new Payload;

        $cycleTime = $payload->getCycleTime();

        $this->assertTrue($payload->getCycleTime()->gt($cycleTime));

        $payload->setResult(true);
        $payload->complete();

        $cycleTime = $payload->getCycleTime();

        $this->assertTrue($payload->getCycleTime()->eq($cycleTime));
    }

    #[DataProvider('withoutData')]
    public function testWithout(string $key, Closure $closure, Closure $setUp = null): void
    {
        $payload = new Payload;

        if ($setUp) {
            /** @var Payload $payload */
            $payload = $setUp($payload);
        }

        $this->assertArrayHasKey($key, $payload->all());

        /** @var Payload $payload */
        $payload = $closure($payload);

        $this->assertArrayNotHasKey($key, $payload->all());
    }

    public static function withoutData(): array
    {
        return [
            'withoutStartedAt' => [
                'started_at',
                fn(Payload $payload) => $payload->withoutStartedAt(),
                null,
            ],
            'withoutArguments' => [
                'arguments',
                fn(Payload $payload) => $payload->withoutArguments(),
                fn(Payload $payload) => $payload->setArguments([]),
            ],
            'withoutResult' => [
                'result',
                fn(Payload $payload) => $payload->withoutResult(),
                fn(Payload $payload) => $payload->setResult(true),
            ],
            'withoutException' => [
                'exception',
                fn(Payload $payload) => $payload->withoutException(),
                fn(Payload $payload) => $payload->setException(new RuntimeException),
            ],
            'withoutCompletedAt' => [
                'completed_at',
                fn(Payload $payload) => $payload->withoutCompletedAt(),
                fn(Payload $payload) => $payload->complete(),
            ],
        ];
    }
}