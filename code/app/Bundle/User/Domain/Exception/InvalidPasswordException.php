<?php

namespace App\Bundle\User\Domain\Exception;

use Exception;

final class InvalidPasswordException extends Exception
{
    public function __construct(string $message = 'Invalid password format')
    {
        parent::__construct($message);
    }
}
