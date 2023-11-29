<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;

trait Tappable
{
    /**
     * Call the given Closure then return the action result.
     *
     * @param callable $decorator
     * @return ActionWrapper|static
     */
    public function tap(callable $decorator): ActionWrapper|static
    {
        return $this->after(fn(mixed $result) => tap($result, $decorator));
    }

    /**
     * When the condition is truthy call the given Closure then return the action result.
     *
     * @param mixed $condition
     * @param callable $callable
     * @return ActionWrapper|static
     */
    public function tapWhen(mixed $condition, callable $callable): ActionWrapper|static
    {
        return $this->when($condition, fn(mixed $result) => tap($result, $callable));
    }
}