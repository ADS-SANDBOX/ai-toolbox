<?php

namespace App\Bundle\User\Domain\Exception;

use Exception;

final class InvalidCredentialsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid credentials provided');
    }
}
