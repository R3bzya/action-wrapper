<?php

namespace R3bzya\ActionWrapper\Concerns;

use R3bzya\ActionWrapper\ActionWrapper;
use R3bzya\ActionWrapper\Support\Decorators\Payload\ViaPayload;
use R3bzya\ActionWrapper\Support\Payloads\Payload;

trait HasPayload
{
    /**
     * Get the payload when the condition is trusty.
     */
    public function payload(callable $callable): ActionWrapper|static
    {
        return $this->through(new ViaPayload($callable));
    }

    /**
     * Apply callable on a payload if the given value is truthy.
     */
    public function payloadWhen(callable $callable, mixed $value): ActionWrapper|static
    {
        return $this->payload(function (Payload $payload) use ($callable, $value) {
            if (value($value, $payload)) {
                $callable($payload);
            }
        });
    }

    /**
     * Apply callable on a payload if the given value is falsy.
     */
    public function payloadUnless(callable $callable, mixed $value): ActionWrapper|static
    {
        return $this->payload(function (Payload $payload) use ($callable, $value) {
            if (! value($value, $payload)) {
                $callable($payload);
            }
        });
    }
}