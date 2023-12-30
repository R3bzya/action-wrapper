<?php

use JetBrains\PhpStorm\Pure;
use R3bzya\ActionWrapper\Support\FluentAction;

if (! function_exists('wrapper')) {
    /**
     * Create a new wrapper instance.
     *
     * @return FluentAction
     */
    #[Pure]
    function wrapper(): FluentAction
    {
        return new FluentAction;
    }
}