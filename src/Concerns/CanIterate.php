<?php

namespace R3bzya\ActionWrapper\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait CanIterate
{
    /**
     * Run each element of the array through an action method.
     */
    public function each(mixed $items, string $method = 'execute'): Collection
    {
        return collect($items)->map(function (mixed $item) use ($method) {
            return $item instanceof Closure
                ? $this->{$method}(...Arr::wrap($item()))
                : $this->{$method}($item);
        });
    }
}