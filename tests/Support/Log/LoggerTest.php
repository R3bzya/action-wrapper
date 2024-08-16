<?php

namespace R3bzya\ActionWrapper\Tests\Support\Log;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use R3bzya\ActionWrapper\Support\Log\Logger;
use R3bzya\ActionWrapper\Tests\TestCase;

class LoggerTest extends TestCase
{
    #[DataProvider('prepareChannelsData')]
    public function testPrepareChannels(string $channels, Closure $validator): void
    {
        $prepareChannels = Closure::bind(function ($channels) {
            return $this->prepareChannels($channels);
        }, new Logger, Logger::class);

        $validator($prepareChannels($channels));
    }

    public static function prepareChannelsData(): array
    {
        return [
            'assert channels are empty' => [
                '',
                function (array $channels) {
                    static::assertEmpty($channels);
                }
            ],
            'assert an on-demand channel and other channels are empty' => [
                ',',
                function (array $channels) {
                    static::assertEmpty($channels);
                }
            ],
            'assert channels are blank' => [
                ' , ',
                function (array $channels) {
                    static::assertEmpty($channels);
                }
            ],
            'assert an on-demand channel is exist and other channels are empty' => [
                'test,',
                function (array $channels) {
                    static::assertCount(1, $channels);
                    static::assertInstanceOf(LoggerInterface::class, $channels[0]);
                }
            ],
            'assert an on-demand channel is exist and other channels are not empty' => [
                ',stack',
                function (array $channels) {
                    static::assertCount(1, $channels);
                    static::assertSame(['stack'], $channels);
                }
            ],
            'assert an on-demand channel is empty and two other channels are exist' => [
                ',stack,slack',
                function (array $channels) {
                    static::assertCount(2, $channels);
                    static::assertSame(['stack', 'slack'], $channels);
                }
            ],
            'assert an on-demand channel and other channels are exist' => [
                'test,stack',
                function (array $channels) {
                    static::assertCount(2, $channels);
                    static::assertInstanceOf(LoggerInterface::class, $channels[0]);
                    static::assertSame('stack', $channels[1]);
                }
            ],
            'assert other channels are unique' => [
                ',stack,stack',
                function (array $channels) {
                    static::assertCount(1, $channels);
                    static::assertSame(['stack'], $channels);
                }
            ],
        ];
    }
}
