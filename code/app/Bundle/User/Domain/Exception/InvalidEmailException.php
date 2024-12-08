<?php

namespace App\Bundle\User\Domain\Exception;

use Exception;

final class InvalidEmailException extends Exception
{
    public function __construct(string $email)
    {
        parent::__construct("Invalid email format: {$email}");
    }
}
