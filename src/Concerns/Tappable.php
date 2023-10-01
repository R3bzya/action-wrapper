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
}