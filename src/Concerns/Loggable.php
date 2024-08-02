<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;
use R3bzya\ActionWrapper\Contracts\Support\Payloads\Payload;
use R3bzya\ActionWrapper\Exceptions\NotDoneException;
use R3bzya\ActionWrapper\Support\Facades\Log;
use Stringable;

trait Loggable
{
    /**
     * Write the logs here.
     */
    public function log(callable $callable, mixed $value = true): ActionWrapper|static
    {
        return $this->payloadWhen($callable, $value);
    }

    /**
     * Log an exception if the exception is thrown.
     */
    public function logExceptions(string|Stringable $message, mixed $value = true): ActionWrapper|static
    {
        return $this->log(function (Payload $payload) use ($message) {
            Log::critical($message, ['exception' => $payload->getException()]);
        }, function (Payload $payload) use ($value) {
            return $payload->hasException() && value($value, $payload);
        });
    }

    /**
     * Log action arguments.
     */
    public function logArguments(string|Stringable $message, mixed $value = true): ActionWrapper|static
    {
        return $this->log(function (Payload $payload) use ($message) {
            Log::info($message, ['args' => $payload->getArguments()]);
        }, fn(Payload $payload) => value($value, $payload));
    }

    /**
     * Log a result if the result is present.
     */
    public function logResult(string|Stringable $message, mixed $value = true): ActionWrapper|static
    {
        return $this->log(function (Payload $payload) use ($message) {
            Log::info($message, ['result' => $payload->getResult()]);
        }, function (Payload $payload) use ($value) {
            return $payload->hasResult() && value($value, $payload);
        });
    }

    /**
     * Log an action performance.
     */
    public function logPerformance(string|Stringable $message, mixed $value = true): ActionWrapper|static
    {
        return $this->log(function (Payload $payload) use ($message) {
            Log::info($message, ['ms' => $payload->getCycleTime()->totalMilliseconds]);
        }, fn(Payload $payload) => value($value, $payload));
    }

    /**
     * Log action data if an action is not done.
     */
    public function logIfNotDone(callable|Stringable|string $message = null, mixed $value = true): ActionWrapper|static
    {
        return $this->log(is_callable($message) ? $message : function (Payload $payload) use ($message) {
            Log::warning($message ?: (
                $payload->hasException() ? $payload->getException()->getMessage() : 'The action is not done.'
            ), $payload->all());
        }, function (Payload $payload) use ($value) {
            if ($payload->hasException() && $payload->getException() instanceof NotDoneException) {
                return value($value, $payload);
            } elseif ($payload->hasResult() && $payload->getResult() === false) {
                return value($value, $payload);
            }

            return false;
        });
    }

    /**
     * Log action data if an action fails.
     */
    public function logIfFailed(callable|Stringable|string $message = null, mixed $value = true): ActionWrapper|static
    {
        return $this->log(is_callable($message) ? $message : function (Payload $payload) use ($message) {
            Log::warning($message ?: (
                $payload->hasException() ? $payload->getException()->getMessage() : 'The action is failed.'
            ), $payload->all());
        }, fn(Payload $payload) => $payload->fails() && value($value, $payload));
    }
}