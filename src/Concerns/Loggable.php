<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;
use R3bzya\ActionWrapper\Support\Facades\Log;
use R3bzya\ActionWrapper\Support\Payloads\Payload;
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
     * Log an action arguments.
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
            Log::info($message, ['ms' => $payload->getRunningTime()]);
        }, fn(Payload $payload) => value($value, $payload));
    }

    /**
     * Log payload data if an action is not done.
     */
    public function logIfNotDone(callable $writer = null, mixed $value = true): ActionWrapper|static
    {
        return $this->log($writer ?: function (Payload $payload) {
            Log::warning('The action is not done.', $payload->toArray());
        }, function (Payload $payload) use ($value) {
            return $payload->isNotCompleted() && value($value, $payload);
        });
    }
}