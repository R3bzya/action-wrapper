<?php

namespace R3bzya\ActionWrapper\Tests\Components\Actions;

use R3bzya\ActionWrapper\Traits\HasActionWrapper;

class DummyAction
{
    use HasActionWrapper;

    public function execute(mixed $value): mixed
    {
        return $value;
    }
}