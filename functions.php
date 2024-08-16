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

if (! function_exists('array_filter_recursive')) {
    function array_filter_recursive(array $array, callable|null $callback = null, int $mode = 1): array {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = array_filter_recursive($value, $callback, $mode);
            }
        }

        return array_filter($array, $callback, $mode);
    }
}