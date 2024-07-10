<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;
use R3bzya\ActionWrapper\Support\Decorators\Retryable\Retry;

trait Retryable
{
    /**
     * Retry the action while it has an exception.
     *
     * @param int $attempts
     * @return ActionWrapper|static
     */
    public function retry(int $attempts): ActionWrapper|static
    {
        return $this->through(new Retry($attempts));
    }
}