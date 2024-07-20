<?php

namespace R3bzya\ActionWrapper\Support\Payloads;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use Throwable;

class Payload implements Arrayable
{
    private readonly Carbon $startedAt;

    private mixed $result;

    private Throwable $exception;

    public function __construct(private readonly array $arguments = [])
    {
        $this->startedAt = Carbon::now();
    }

    public function setResult(mixed $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function setException(Throwable $exception): static
    {
        $this->exception = $exception;

        return $this;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function hasNotResult(): bool
    {
        return ! $this->hasResult();
    }

    public function hasResult(): bool
    {
        return isset($this->result);
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function hasNotException(): bool
    {
        return ! $this->hasException();
    }

    public function hasException(): bool
    {
        return isset($this->exception);
    }

    public function getStartedAt(): Carbon
    {
        return $this->startedAt;
    }

    public function passes(): bool
    {
        return $this->hasNotException() && $this->hasResult();
    }

    public function fails(): bool
    {
        return ! $this->passes();
    }

    public function isCompleted(): bool
    {
        return $this->passes() && $this->getResult() !== false;
    }

    public function isNotCompleted(): bool
    {
        return ! $this->isCompleted();
    }

    /**
     * @throws Throwable
     */
    public function fireException(): void
    {
        if ($this->hasException()) {
            throw $this->exception;
        }
    }

    public function toArray(): array
    {
        $data = [
            'passes' => $this->passes(),
            'arguments' => $this->arguments,
            'started_at' => $this->startedAt,
        ];

        if ($this->hasException()) {
            $data['exception'] = $this->exception;
        }

        if ($this->hasResult()) {
            $data['result'] = $this->result;
        }

        return $data;
    }

    public function getRunningTime(): float
    {
        return $this->startedAt->diffInMilliseconds(Carbon::now());
    }
}