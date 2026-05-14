<?php

namespace App\Exceptions;

use App\Enums\OrderStatus;
use Exception;

class InvalidOrderTransitionException extends Exception
{
    public function __construct(
        public readonly OrderStatus $from,
        public readonly OrderStatus $to,
        string $message
    ) {
        parent::__construct($message);
    }
}