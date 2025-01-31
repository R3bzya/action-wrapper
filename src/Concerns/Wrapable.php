<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;

trait Wrapable
{
    /**
     * Wrap the result in the given class.
     */
    public function wrap(string $class): ActionWrapper|static
    {
        return $this->after(function (mixed $result) use ($class) {
            return new $class(...(is_array($result) ? $result : [$result]));
        });
    }
}