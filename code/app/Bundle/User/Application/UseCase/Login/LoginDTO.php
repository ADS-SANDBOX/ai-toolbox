<?php

namespace App\Bundle\User\Application\UseCase\Login;

final readonly class LoginDTO
{
    public function __construct(
        private string $email,
        private string $password
    ) {}

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }
}
