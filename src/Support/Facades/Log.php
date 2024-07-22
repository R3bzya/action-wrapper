<?php

namespace R3bzya\ActionWrapper\Support\Facades;

use Illuminate\Support\Facades\Facade;
use R3bzya\ActionWrapper\Support\Log\Logger;
use Stringable;

/**
 * @method static void emergency(string|Stringable $message, array $context = [])
 * @method static void alert(string|Stringable $message, array $context = [])
 * @method static void critical(string|Stringable $message, array $context = [])
 * @method static void error(string|Stringable $message, array $context = [])
 * @method static void warning(string|Stringable $message, array $context = [])
 * @method static void notice(string|Stringable $message, array $context = [])
 * @method static void info(string|Stringable $message, array $context = [])
 * @method static void debug(string|Stringable $message, array $context = [])
 *
 * @see Logger
 */
class Log extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Logger::class;
    }
}