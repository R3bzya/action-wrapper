<?php

namespace R3bzya\ActionWrapper\Exceptions;

use Exception;
use Throwable;

class NotDoneException extends Exception
{
    public function __construct(string $message = 'Not done', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}