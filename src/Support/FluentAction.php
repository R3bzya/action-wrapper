<?php

namespace R3bzya\ActionWrapper\Support;

use Closure;
use R3bzya\ActionWrapper\Support\Traits\HasActionWrapper;

class FluentAction
{
    use HasActionWrapper;

    public function execute(mixed $value, mixed ...$args): mixed
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}