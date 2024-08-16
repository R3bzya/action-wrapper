<?php

namespace R3bzya\ActionWrapper\Tests\Concerns;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;
use R3bzya\ActionWrapper\Support\Payloads\Payload;
use R3bzya\ActionWrapper\Tests\TestCase;
use RuntimeException;

class LoggableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Log::shouldReceive('build', 'stack')
            ->andReturnSelf();
    }

    #[DataProvider('logData')]
    public function testLog(int $expectedAssertionsCount, mixed $value): void
    {
        wrapper()
            ->log(fn() => $this->addToAssertionCount(1), $value)
            ->execute(true);

        $this->assertSame($expectedAssertionsCount, $this->numberOfAssertionsPerformed());
    }

    public static function logData(): array
    {
        return [
            [1, true],
            [0, false],
            [1, fn(Payload $payload) => $payload->passes()],
            [0, fn(Payload $payload) => $payload->fails()],
        ];
    }

    public function testLogException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action was failed.');

        Log::shouldReceive('critical')
            ->once()
            ->withArgs(function (string $message, array $context = []) {
                $this->assertSame('logExceptions', $message);
                $this->assertArrayHasKey('exception', $context);
                $this->assertInstanceOf(RuntimeException::class, $context['exception']);
                $this->assertSame('Action was failed.', $context['exception']->getMessage());

                return true;
            });

        wrapper()
            ->logExceptions('logExceptions')
            ->execute(fn() => throw new RuntimeException('Action was failed.'));
    }

    public function testShouldNotLogException(): void
    {
        Log::shouldReceive('critical')
            ->once()
            ->never();

        $this->assertFalse(wrapper()->logExceptions('logExceptions')->execute(false));
    }

    public function testLogArguments(): void
    {
        $this->assertLog('info', 'logArguments', ['args' => [true]]);

        $this->assertTrue(wrapper()->logArguments('logArguments')->execute(true));
    }

    public function testLogResult(): void
    {
        $this->assertLog('info', 'logResult', ['result' => true]);

        $this->assertTrue(wrapper()->logResult('logResult')->execute(true));
    }

    public function testShouldNotLogResult(): void
    {
        Log::shouldReceive('info')
            ->never();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action was failed.');

        wrapper()->logResult('logResult')->execute(fn() => throw new RuntimeException('Action was failed.'));
    }

    public function testLogPerformance(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function (string $message, array $context = []) {
                $this->assertSame('logPerformance', $message);
                $this->assertArrayHasKey('ms', $context);

                return true;
            });

        $this->assertTrue(wrapper()->logPerformance('logPerformance')->execute(true));
    }

    public function testLogIfNotDone(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function (string $message, array $context = []) {
                $this->assertEquals([
                    'started_at',
                    'arguments',
                    'result',
                    'completed_at',
                ], array_keys($context));

                $this->assertSame('The action is not done.', $message);
                $this->assertSame([false], $context['arguments']);
                $this->assertFalse($context['result']);
                $this->assertInstanceOf(Carbon::class, $context['started_at']);
                $this->assertInstanceOf(Carbon::class, $context['completed_at']);
                $this->assertArrayNotHasKey('exception', $context);

                return true;
            });

        $this->assertFalse(wrapper()->logIfNotDone()->execute(false));
    }

    public function testLogIfFailed(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The action is failed.');

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function (string $message, array $context = []) {
                $this->assertEquals([
                    'started_at',
                    'arguments',
                    'exception',
                    'completed_at',
                ], array_keys($context));

                $this->assertSame('The action is failed.', $message);
                $this->assertIsArray($context['arguments']);
                $this->assertInstanceOf(Carbon::class, $context['started_at']);
                $this->assertInstanceOf(Carbon::class, $context['completed_at']);
                $this->assertArrayNotHasKey('result', $context);

                return true;
            });

        $this->assertFalse(wrapper()->logIfFailed()->execute(fn() => throw new RuntimeException('The action is failed.')));
    }

    private function assertLog(string $method, string $expectedMessage, array $expectedContext = []): void
    {
        Log::shouldReceive($method)
            ->once()
            ->withArgs(function (string $message, array $context = []) use ($expectedMessage, $expectedContext) {
                $this->assertSame($expectedMessage, $message);
                $this->assertSame($expectedContext, $context);

                return true;
            });
    }
}