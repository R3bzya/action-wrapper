<?php

namespace R3bzya\ActionWrapper\Exceptions;

use Exception;
use Illuminate\Support\Facades\Lang;
use Throwable;

class NotDoneException extends Exception
{
    public function __construct(string $message = 'Action not done.', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(Lang::get($message), $code, $previous);
    }
}