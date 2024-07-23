<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;

trait Conditionable
{
    /**
     * Apply the given callback if the given value is truthy.
     */
    public function when(mixed $value, callable $callable): ActionWrapper|static
    {
        return $this->after(function (mixed $result) use ($value, $callable) {
            return value($value, $result) ? $callable($result) : $result;
        });
    }

    /**
     * Apply the given callback if the given value is falsy.
     */
    public function unless(mixed $value, callable $callable): ActionWrapper|static
    {
        return $this->after(function (mixed $result) use ($value, $callable) {
            return value($value, $result) ? $result : $callable($result);
        });
    }
}