<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;
use R3bzya\ActionWrapper\Support\Decorators\Retryable\Retry;

trait Retryable
{
    /**
     * Retry the action if it has an exception.
     */
    public function retry(int $attempts): ActionWrapper|static
    {
        return $this->through(new Retry($attempts));
    }
}