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
     * Throw an exception when the result equals to condition.
     *
     * @param mixed $condition
     * @param Throwable $throwable
     * @param bool $strict
     * @return ActionWrapper|static
     */
    public function throwIf(
        mixed $condition,
        Throwable $throwable = new RuntimeException,
        bool $strict = false,
    ): ActionWrapper|static
    {
        return $this->throwWhen(function (mixed $result) use ($condition, $strict) {
            $condition = $condition instanceof Closure ? $condition($result) : $condition;

            return $strict ? $result === $condition : $result == $condition;
        }, $throwable);
    }

    /**
     * Throw an exception when the result not equals to condition.
     *
     * @param mixed $condition
     * @param Throwable $throwable
     * @param bool $strict
     * @return ActionWrapper|static
     */
    public function throwIfNot(
        mixed $condition,
        Throwable $throwable = new RuntimeException,
        bool $strict = false,
    ): ActionWrapper|static
    {
        return $this->throwUnless(function (mixed $result) use ($condition, $strict) {
            $condition = $condition instanceof Closure ? $condition($result) : $condition;

            return $strict ? $result === $condition : $result == $condition;
        }, $throwable);
    }

    /**
     * Throw an exception when an action result is false.
     *
     * @param Throwable $throwable
     * @param bool $strict
     * @return ActionWrapper|static
     */
    public function throwIfNotDone(Throwable $throwable = new NotDoneException, bool $strict = true): ActionWrapper|static
    {
        return $this->throwIf(false, $throwable, $strict);
    }
}