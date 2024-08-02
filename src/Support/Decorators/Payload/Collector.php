<?php

namespace R3bzya\ActionWrapper\Support\Decorators\Payload;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use R3bzya\ActionWrapper\Contracts\Support\Payloads\Payload as PayloadContract;
use R3bzya\ActionWrapper\Support\Payloads\Payload;
use Throwable;

class Collector
{
    /**
     * @var callable
     */
    private mixed $callable;

    public function __construct(
        callable $callable,
        private readonly PayloadContract|string|null $payload = null,
    )
    {
        $this->callable = $callable;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(array $arguments, Closure $next): mixed
    {
        $payload = $this->makePayload()
            ->setArguments($arguments);

        try {
            $payload->setResult($next($arguments));
        } catch (Throwable $e) {
            $payload->setException($e);
        }

        return $payload
            ->complete()
            ->apply($this->callable)
            ->validated()
            ->fireException()
            ->getResult();
    }

    /**
     * @throws BindingResolutionException
     */
    protected function makePayload(): PayloadContract
    {
        $payload = $this->payload ?? config('action-wrapper.payload', Payload::class);

        if (! is_string($payload)) {
            return $payload;
        }

        return app()->bound($payload) ? app()->make($payload) : new $payload;
    }
}