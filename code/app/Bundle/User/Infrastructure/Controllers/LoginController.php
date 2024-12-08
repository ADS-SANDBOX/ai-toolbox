<?php

namespace App\Bundle\User\Infrastructure\Controllers;

use App\Bundle\User\Infrastructure\Action\LoginAction;
use App\Bundle\User\Infrastructure\Http\Request\LoginRequest;
use Illuminate\Http\JsonResponse;

final readonly class LoginController
{
    public function __construct(
        private LoginAction $loginAction
    ) {}

    public function __invoke(LoginRequest $loginRequest): JsonResponse
    {
        return ($this->loginAction)(loginRequest: $loginRequest);
    }
}
