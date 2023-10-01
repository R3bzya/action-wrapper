<?php

namespace R3bzya\ActionWrapper\Support\Operators\Equality;

readonly class IsEqual
{
    public function __construct(private mixed $value, private bool $strict = false) {}

    /**
     * Assert the value equality.
     *
     * @param mixed $other
     * @return bool
     */
    public function evaluate(mixed $other): bool
    {
        return $this->strict ? $this->value === $other : $this->value == $other;
    }
}