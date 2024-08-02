<?php

namespace R3bzya\ActionWrapper\Support\Payloads;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use R3bzya\ActionWrapper\Contracts\Support\Payloads\Payload as PayloadContract;
use RuntimeException;
use Throwable;

class Payload implements PayloadContract
{
    private array $items = [];

    public function __construct()
    {
        $this->init();
    }

    protected function init(): static
    {
        return $this->put('started_at', Carbon::now());
    }

    protected function put(string $key, mixed $value): static
    {
        $this->items[$key] = $value;

        return $this;
    }

    protected function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    protected function has(string $key): bool
    {
        return key_exists($key, $this->items);
    }

    protected function forget(string $key): static
    {
        unset($this->items[$key]);

        return $this;
    }

    public function clone(): static
    {
        return clone $this;
    }

    protected function without(string $key): static
    {
        return $this->clone()->forget($key);
    }

    public function setArguments(array $arguments): static
    {
        return $this->put('arguments', $arguments);
    }

    public function getArguments(): array
    {
        return $this->get('arguments', []);
    }

    public function withoutArguments(): static
    {
        return $this->without('arguments');
    }

    public function getStartedAt(): Carbon
    {
        return $this->get('started_at');
    }

    public function withoutStartedAt(): static
    {
        return $this->without('started_at');
    }

    public function setResult(mixed $value): static
    {
        return $this->put('result', $value);
    }

    /**
     * @throws RuntimeException
     */
    public function getResult(): mixed
    {
        if (! $this->hasResult()) {
            throw new RuntimeException('The result does not exist in the payload.');
        }

        return $this->get('result');
    }

    public function hasResult(): bool
    {
        return $this->has('result');
    }

    public function forgetResult(): static
    {
        return $this->forget('result');
    }

    public function withoutResult(): static
    {
        return $this->without('result');
    }

    public function setException(Throwable $exception): static
    {
        return $this->put('exception', $exception);
    }

    /**
     * @throws RuntimeException
     */
    public function getException(): Throwable
    {
        if (! $this->hasException()) {
            throw new RuntimeException('The exception does not exist in the payload.');
        }

        return $this->get('exception');
    }

    public function hasException(): bool
    {
        return ($this->items['exception'] ?? null) instanceof Throwable;
    }

    public function forgetException(): static
    {
        return $this->forget('exception');
    }

    public function withoutException(): static
    {
        return $this->without('exception');
    }

    /**
     * @throws Throwable
     */
    public function fireException(): static
    {
        if ($this->hasException()) {
            throw $this->getException();
        }

        return $this;
    }

    public function passes(): bool
    {
        return ! $this->hasException() && $this->hasResult();
    }

    public function fails(): bool
    {
        return ! $this->passes();
    }

    public function complete(Carbon $completedAt = null): static
    {
        if ($this->isCompleted()) {
            return $this;
        }

        return $this->put('completed_at', $completedAt ?: Carbon::now());
    }

    public function incomplete(): static
    {
        return $this->forget('completed_at');
    }

    public function getCompletedAt(): ?Carbon
    {
        return $this->get('completed_at');
    }

    public function isCompleted(): bool
    {
        return isset($this->items['completed_at']);
    }

    public function withoutCompletedAt(): static
    {
        return $this->without('completed_at');
    }

    public function getCycleTime(): CarbonInterval
    {
        return $this->getStartedAt()->diff($this->getCompletedAt() ?: Carbon::now());
    }

    public function all(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return collect($this->items)->toArray();
    }

    public function validate(): bool
    {
        return $this->hasResult() || $this->hasException();
    }

    public function validated(): static
    {
        if (! $this->validate()) {
            throw new RuntimeException('The payload is invalid');
        }

        return $this;
    }

    public function apply(callable $callable): static
    {
        call_user_func($callable, $this);

        return $this;
    }
}