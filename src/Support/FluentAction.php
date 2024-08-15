<?php

namespace R3bzya\ActionWrapper\Support;

class FluentAction extends Action
{
    public function execute(mixed $value, mixed ...$args): mixed
    {
        return value($value, ...$args);
    }
}