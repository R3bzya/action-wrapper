<?php

namespace R3bzya\ActionWrapper\Contracts\Support\Payloads;

use Carbon\CarbonInterval;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use Throwable;

interface Payload extends Arrayable
{
    /**
     * Set the action arguments to the payload.
     */
    public function setArguments(array $arguments): static;

    /**
     * Get the action arguments from the payload.
     */
    public function getArguments(): array;

    /**
     * Get the time when the payload was created
     */
    public function getStartedAt(): Carbon;

    /**
     * Set the action result to the payload.
     */
    public function setResult(mixed $value): static;

    /**
     * Get the action result to the payload.
     */
    public function getResult(): mixed;

    /**
     * Determine if the payload has an action result.
     */
    public function hasResult(): bool;

    /**
     * Forget the action result in the payload.
     */
    public function forgetResult(): static;

    /**
     * Set the action exception to the payload.
     */
    public function setException(Throwable $exception): static;

    /**
     * Get the action exception to the payload.
     */
    public function getException(Throwable|null $default = null): Throwable|null;

    /**
     * Determine if the payload has an action exception.
     */
    public function hasException(): bool;

    /**
     * Forget the action exception in the payload.
     */
    public function forgetException(): static;

    /**
     * Throw the exception when the exception exists.
     */
    public function fireException(): static;

    /**
     * Determine if the action was not performed.
     */
    public function fails(): bool;

    /**
     * Complete payload when the action was finished.
     */
    public function complete(Carbon $completedAt = null): static;

    /**
     * Rollback the complete function effect.
     */
    public function incomplete(): static;

    /**
     * Get the time when the payload was completed.
     */
    public function getCompletedAt(): ?Carbon;

    /**
     * Determine if the payload has completed.
     */
    public function isCompleted(): bool;

    /**
     * Get the time it takes from when the payload is created to when it is completed.
     */
    public function getCycleTime(): CarbonInterval;

    /**
     * Get all payload attributes.
     */
    public function all(): array;

    /**
     * Validate payload attributes.
     */
    public function validate(): bool;

    /**
     * Validate payload attributes then return the payload.
     */
    public function validated(): static;

    /**
     * Apply the given callable on the payload.
     */
    public function apply(callable $callable): static;
}