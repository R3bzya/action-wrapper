<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;
use R3bzya\ActionWrapper\Contracts\Support\Payloads\Payload;
use R3bzya\ActionWrapper\Support\Decorators\Payload\Collector;

trait HasPayload
{
    /**
     * Get the payload when the condition is trusty.
     */
    public function payload(callable $callable, Payload|string|null $payload = null): ActionWrapper|static
    {
        return $this->through(new Collector($callable, $payload));
    }

    /**
     * Apply callable on a payload if the given value is truthy.
     */
    public function payloadWhen(callable $callable, mixed $value, Payload|string|null $payload = null): ActionWrapper|static
    {
        return $this->payload(function (Payload $payload) use ($callable, $value) {
            if (value($value, $payload)) {
                $callable($payload);
            }
        }, $payload);
    }

    /**
     * Apply callable on a payload if the given value is falsy.
     */
    public function payloadUnless(callable $callable, mixed $value, Payload|string|null $payload = null): ActionWrapper|static
    {
        return $this->payload(function (Payload $payload) use ($callable, $value) {
            if (! value($value, $payload)) {
                $callable($payload);
            }
        }, $payload);
    }
}