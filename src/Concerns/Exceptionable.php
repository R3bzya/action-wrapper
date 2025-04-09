<?php

namespace R3bzya\ActionWrapper\Concerns;

use Closure;
use Illuminate\Contracts\Support\Responsable;
use R3bzya\ActionWrapper\ActionWrapper;
use R3bzya\ActionWrapper\Exceptions\NotDoneException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait Exceptionable
{
    /**
     * Defines how to handle to a thrown exception.
     */
    public function catch(mixed $value): ActionWrapper|static
    {
        return $this->through(function (array $arguments, Closure $next) use ($value) {
            try {
                return $next($arguments);
            } catch (Throwable $e) {
                return value($value, $e);
            }
        });
    }

    /**
     * Returns false when an exception is thrown.
     */
    public function falseInsteadOfThrowable(): ActionWrapper|static
    {
        return $this->catch(false);
    }

    /**
     * Returns an exception to respond without throwing an exception.
     */
    public function throwableInsteadOfThrow(): ActionWrapper|static
    {
        return $this->catch(fn(Throwable $e) => $e);
    }

    /**
     * Throw the given exception if the result is true.
     */
    public function throwIf(
        Throwable|string $exception = new RuntimeException,
        ...$parameters,
    ): ActionWrapper|static
    {
        return $this->after(fn(mixed $result) => throw_if($result, $exception, ...$parameters));
    }

    /**
     * Throw the given exception unless the result is true.
     */
    public function throwUnless(
        Throwable|string $exception = new RuntimeException,
        ...$parameters,
    ): ActionWrapper|static
    {
        return $this->after(fn(mixed $result) => throw_unless($result, $exception, ...$parameters));
    }

    /**
     * Throw an exception when the result of an action is false.
     */
    public function throwIfNotDone(Throwable $throwable = new NotDoneException): ActionWrapper|static
    {
        return $this->when(fn(mixed $result) => $result === false, fn() => throw $throwable);
    }

    /**
     * Throw an HttpException with the given data if the result is true.
     */
    public function abortIf(
        Response|Responsable|int $code,
        string $message = '',
        array $headers = [],
    ): ActionWrapper|static
    {
        $abortHandler = function (mixed $result) use ($code, $message, $headers) {
            abort_if($result, $code, $message, $headers);

            return $result;
        };

        return $this->after($abortHandler);
    }

    /**
     * Throw an HttpException with the given data unless the result is true.
     */
    public function abortUnless(
        Response|Responsable|int $code,
        string $message = '',
        array $headers = [],
    ): ActionWrapper|static
    {
        $abortHandler = function (mixed $result) use ($code, $message, $headers) {
            abort_unless($result, $code, $message, $headers);

            return $result;
        };

        return $this->after($abortHandler);
    }
}