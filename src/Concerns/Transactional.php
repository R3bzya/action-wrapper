<?php

namespace R3bzya\ActionWrapper\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;
use R3bzya\ActionWrapper\ActionWrapper;

trait Transactional
{
    /**
     * Execute an action within a transaction.
     */
    public function transaction(int $attempts = 1): ActionWrapper|static
    {
        return $this->through(function (array $arguments, Closure $next) use ($attempts) {
            return DB::transaction(fn() => $next($arguments), $attempts);
        });
    }
}