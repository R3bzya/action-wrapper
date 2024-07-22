<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;

trait Tappable
{
    /**
     * Call the given Closure then return the action result.
     */
    public function tap(callable $decorator = null): ActionWrapper|static
    {
        return $this->after(fn(mixed $result) => tap($result, $decorator));
    }

    /**
     * When the condition is truthy call the given Closure, then return the action result.
     */
    public function tapWhen(mixed $condition, callable $callable = null): ActionWrapper|static
    {
        return $this->when($condition, fn(mixed $result) => tap($result, $callable));
    }

    /**
     * When the condition is falsy call the given Closure, then return the action result.
     */
    public function tapUnless(mixed $condition, callable $callable = null): ActionWrapper|static
    {
        return $this->unless($condition, fn(mixed $result) => tap($result, $callable));
    }
}