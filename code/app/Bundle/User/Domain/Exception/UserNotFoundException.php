<?php

namespace App\Bundle\User\Domain\Exception;

use Exception;

final class UserNotFoundException extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct("User with id {$id} not found");
    }
}
