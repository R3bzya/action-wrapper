<?php

namespace R3bzya\ActionWrapper\Support\Traits;

use R3bzya\ActionWrapper\Concerns\Conditionable;
use R3bzya\ActionWrapper\Concerns\Exceptionable;
use R3bzya\ActionWrapper\Concerns\InteractsWithModel;
use R3bzya\ActionWrapper\Concerns\Tappable;
use R3bzya\ActionWrapper\Concerns\Transactional;
use R3bzya\ActionWrapper\Support\Traits\Simples\HasActionWrapper as HasSimpleActionWrapper;

trait HasActionWrapper
{
    use HasSimpleActionWrapper,
        Conditionable,
        Tappable,
        Transactional,
        Exceptionable,
        InteractsWithModel;
}