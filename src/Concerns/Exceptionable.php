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