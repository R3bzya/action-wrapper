<?php

namespace R3bzya\ActionWrapper\Actions;

use Closure;
use R3bzya\ActionWrapper\Traits\HasActionWrapper;

class FluentAction
{
    use HasActionWrapper;

    public function execute(mixed $value, mixed ...$args): mixed
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}