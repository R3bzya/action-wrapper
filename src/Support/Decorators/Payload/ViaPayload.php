<?php

namespace R3bzya\ActionWrapper\Support\Decorators\Payload;

use Closure;
use R3bzya\ActionWrapper\Support\Payloads\Payload;
use Throwable;

readonly class ViaPayload
{
    /**
     * @var callable
     */
    private mixed $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(array $arguments, Closure $next): mixed
    {
        $payload = new Payload($arguments);

        try {
            $payload->setResult($next($arguments));
        } catch (Throwable $e) {
            $payload->setException($e);
        } finally {
            call_user_func($this->callable, $payload);
        }

        $payload->fireException();

        return $payload->getResult();
    }
}