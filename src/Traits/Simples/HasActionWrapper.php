<?php

namespace R3bzya\ActionWrapper\Traits\Simples;

use Closure;
use R3bzya\ActionWrapper\ActionWrapper;

trait HasActionWrapper
{
    protected ActionWrapper $actionWrapper;

    /**
     * The function names which should be decorated.
     *
     * @return string[]
     */
    protected function decoratedMethods(): array
    {
        return ['execute'];
    }

    /**
     * Create a new ActionWrapper instance.
     *
     * @return ActionWrapper|static
     */
    public function makeActionWrapper(): ActionWrapper|static
    {
        return new ActionWrapper($this, $this->decoratedMethods());
    }

    /**
     * Get or create the action wrapper.
     *
     * @return ActionWrapper|static
     */
    public function getActionWrapper(): ActionWrapper|static
    {
        return $this->actionWrapper ?? $this->actionWrapper = $this->makeActionWrapper();
    }

    /**
     * Add the callback through which the action will be sent.
     *
     * @param callable $decorator
     * @return ActionWrapper|static
     */
    public function through(callable $decorator): ActionWrapper|static
    {
        return $this->getActionWrapper()->through($decorator);
    }

    /**
     * Call the given Closure before an action execution.
     *
     * @param callable $decorator
     * @return ActionWrapper|static
     */
    public function before(callable $decorator): ActionWrapper|static
    {
        return $this->through(function (array $arguments, Closure $next) use ($decorator) {
            $result = $decorator(...$arguments);

            if ($result === false) {
                return false;
            }

            return $next(is_array($result) ? $result : $arguments);
        });
    }

    /**
     * Call the given Closure after an action execution.
     *
     * @param callable $decorator
     * @return ActionWrapper|static
     */
    public function after(callable $decorator): ActionWrapper|static
    {
        return $this->through(fn(array $arguments, Closure $next) => $decorator($next($arguments)));
    }

    /**
     * Remove all pipes from the action wrapper.
     *
     * @return ActionWrapper|static
     */
    public function resetActionWrapper(): ActionWrapper|static
    {
        return $this->getActionWrapper()->forgetPipes();
    }

    /**
     * Unset the action wrapper.
     *
     * @return ActionWrapper|static
     */
    public function forgetActionWrapper(): ActionWrapper|static
    {
        unset($this->actionWrapper);

        return $this;
    }
}