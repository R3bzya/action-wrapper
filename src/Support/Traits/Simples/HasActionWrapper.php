<?php

namespace R3bzya\ActionWrapper\Support\Traits\Simples;

use Closure;
use Illuminate\Support\Traits\Macroable;
use R3bzya\ActionWrapper\ActionWrapper;

trait HasActionWrapper
{
    use Macroable;

    protected ActionWrapper $actionWrapper;

    /**
     * The function names which should be decorated.
     *
     * @return string[]
     */
    protected function decoratedFunctions(): array
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
        return new ActionWrapper($this, $this->decoratedFunctions());
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