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
     * Defines how to respond to a thrown exception.
     *
     * @param mixed $value
     * @return ActionWrapper|static
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
     * Returns false when an exception thrown.
     *
     * @return ActionWrapper|static
     */
    public function safe(): ActionWrapper|static
    {
        return $this->try(false);
    }

    /**
     * Returns an exception to respond without throwing an exception.
     *
     * @return ActionWrapper|static
     */
    public function catch(): ActionWrapper|static
    {
        return $this->try(fn(Throwable $e) => $e);
    }

    /**
     * Throw an exception when condition is truthy.
     *
     * @param mixed $condition
     * @param Throwable $throwable
     * @return ActionWrapper|static
     */
    public function throwWhen(mixed $condition, Throwable $throwable = new RuntimeException): ActionWrapper|static
    {
        return $this->when($condition, fn() => throw $throwable);
    }

    /**
     * Throw an exception when condition is falsy.
     *
     * @param mixed $condition
     * @param Throwable $throwable
     * @return ActionWrapper|static
     */
    public function throwUnless(mixed $condition, Throwable $throwable = new RuntimeException): ActionWrapper|static
    {
        return $this->unless($condition, fn() => throw $throwable);
    }

    /**
     * Throw an exception when an action result is false.
     *
     * @param Throwable $throwable
     * @return ActionWrapper|static
     */
    public function throwIfNotDone(Throwable $throwable = new NotDoneException): ActionWrapper|static
    {
        return $this->throwWhen(fn(mixed $result) => $result === false, $throwable);
    }
}