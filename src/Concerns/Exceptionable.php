<?php

namespace R3bzya\ActionWrapper\Concerns;

use Closure;
use R3bzya\ActionWrapper\ActionWrapper;
use R3bzya\ActionWrapper\Exceptions\NotDoneException;
use RuntimeException;
use Throwable;

trait Exceptionable
{
    /**
     * Defines how to handle to a thrown exception.
     */
    public function try(mixed $value): ActionWrapper|static
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
        return $this->try(false);
    }

    /**
     * Returns an exception to respond without throwing an exception.
     */
    public function catch(): ActionWrapper|static
    {
        return $this->try(fn(Throwable $e) => $e);
    }

    /**
     * Throw an exception if the given value is truthy.
     */
    public function throwWhen(mixed $value, Throwable $throwable = new RuntimeException): ActionWrapper|static
    {
        return $this->when($value, fn() => throw $throwable);
    }

    /**
     * Throw an exception if the given value is falsy.
     */
    public function throwUnless(mixed $value, Throwable $throwable = new RuntimeException): ActionWrapper|static
    {
        return $this->unless($value, fn() => throw $throwable);
    }

    /**
     * Throw an exception when the result of an action is false.
     */
    public function throwIfNotDone(Throwable $throwable = new NotDoneException): ActionWrapper|static
    {
        return $this->throwWhen(fn(mixed $result) => $result === false, $throwable);
    }
}