<?php

namespace R3bzya\ActionWrapper\Support\Decorators\Retryable;

use Closure;
use RuntimeException;
use Throwable;

readonly class Retry
{
    public function __construct(private int $attempts) {}

    public function __invoke(array $arguments, Closure $next): mixed
    {
        for ($currentAttempt = 1; $currentAttempt <= $this->attempts; $currentAttempt++) {
            try {
                return $next($arguments);
            } catch (Throwable $e) {
                if ($currentAttempt >= $this->attempts) {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Unable to retry. Minimum number of attempts: 1.');
    }
}