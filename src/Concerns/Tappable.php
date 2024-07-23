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
     * When the given value is truthy call the given Closure, then return the action result.
     */
    public function tapWhen(mixed $value, callable $callable = null): ActionWrapper|static
    {
        return $this->when($value, fn(mixed $result) => tap($result, $callable));
    }

    /**
     * When the given value is falsy call the given Closure, then return the action result.
     */
    public function tapUnless(mixed $value, callable $callable = null): ActionWrapper|static
    {
        return $this->unless($value, fn(mixed $result) => tap($result, $callable));
    }
}