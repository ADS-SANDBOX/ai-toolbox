<?php

namespace App\Bundle\User\Application\UseCase\RegisterUser;

final readonly class RegisterUserDTO
{
    public function __construct(
        private string $name,
        private string $email,
        private string $password
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }
}
