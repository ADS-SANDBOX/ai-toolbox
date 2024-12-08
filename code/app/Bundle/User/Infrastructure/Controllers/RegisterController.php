<?php

namespace App\Bundle\User\Infrastructure\Controllers;

use App\Bundle\User\Infrastructure\Action\RegisterUserAction;
use App\Bundle\User\Infrastructure\Http\Request\RegisterUserRequest;
use Illuminate\Http\JsonResponse;

final readonly class RegisterController
{
    public function __construct(
        private RegisterUserAction $registerUserAction
    ) {}

    public function __invoke(RegisterUserRequest $registerUserRequest): JsonResponse
    {
        return ($this->registerUserAction)(registerUserRequest: $registerUserRequest);
    }
}
