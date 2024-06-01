<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;

trait Conditionable
{
    /**
     * Apply the given callback when the condition is truthy.
     *
     * @param mixed $condition
     * @param callable $callable
     * @return ActionWrapper|static
     */
    public function when(mixed $condition, callable $callable): ActionWrapper|static
    {
        return $this->after(function (mixed $result) use ($condition, $callable) {
            return value($condition, $result) ? $callable($result) : $result;
        });
    }

    /**
     * Apply the given callback when the condition is falsy.
     *
     * @param mixed $condition
     * @param callable $callable
     * @return ActionWrapper|static
     */
    public function unless(mixed $condition, callable $callable): ActionWrapper|static
    {
        return $this->after(function (mixed $result) use ($condition, $callable) {
            return value($condition, $result) ? $result : $callable($result);
        });
    }
}