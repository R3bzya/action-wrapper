<?php

namespace R3bzya\ActionWrapper\Tests\Components\Decorators;

use Closure;

readonly class PlusDecorator
{
    public function __construct(
        private int $value
    ) {}

    public function __invoke(array $arguments, Closure $next): int
    {
        return $next($arguments) + $this->value;
    }
}