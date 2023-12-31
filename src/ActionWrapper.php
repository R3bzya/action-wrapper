<?php

namespace R3bzya\ActionWrapper;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Traits\ForwardsCalls;

class ActionWrapper
{
    use ForwardsCalls;

    /** @var callable[] */
    private array $pipes = [];

    public function __construct(
        private readonly object $object,
        private readonly array $methods = [],
    ) {}

    public function __call(string $name, array $arguments): mixed
    {
        if (! in_array($name, $this->methods)) {
            return $this->forwardCallTo($this->object, $name, $arguments);
        }

        return (new Pipeline)
            ->send($arguments)
            ->through($this->pipes)
            ->then(fn(array $passable) => $this->forwardCallTo($this->object, $name, $passable));
    }

    /**
     * Add the pipe.
     *
     * @param callable $pipe
     * @return static
     */
    public function through(callable $pipe): static
    {
        $this->pipes[] = $pipe;

        return $this;
    }

    /**
     * Remove all pipes.
     *
     * @return static
     */
    public function forgetPipes(): static
    {
        $this->pipes = [];

        return $this;
    }
}