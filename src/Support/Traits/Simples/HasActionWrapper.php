<?php

namespace R3bzya\ActionWrapper\Support\Traits\Simples;

use Closure;
use Illuminate\Support\Traits\Macroable;
use R3bzya\ActionWrapper\ActionWrapper;

/**
 * @mixin ActionWrapper
 */
trait HasActionWrapper
{
    use Macroable {
        Macroable::__call as macroCall;
    }

    protected ActionWrapper $actionWrapper;

    public function __call(string $name, array $arguments)
    {
        return method_exists($this->getActionWrapper(), $name) && ! static::hasMacro($name)
            ? $this->getActionWrapper()->$name(...$arguments)
            : $this->macroCall($name, $arguments);
    }

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
     */
    public function makeActionWrapper(): ActionWrapper|static
    {
        return new ActionWrapper($this, $this->decoratedFunctions());
    }

    /**
     * Get or create the action wrapper.
     */
    public function getActionWrapper(): ActionWrapper|static
    {
        return $this->actionWrapper ?? $this->actionWrapper = $this->makeActionWrapper();
    }

    /**
     * Call the given Closure before an action execution.
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
     */
    public function after(callable $decorator): ActionWrapper|static
    {
        return $this->through(fn(array $arguments, Closure $next) => $decorator($next($arguments)));
    }

    /**
     * Unset the action wrapper.
     */
    public function forgetActionWrapper(): ActionWrapper|static
    {
        unset($this->actionWrapper);

        return $this;
    }
}