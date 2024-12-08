<?php

namespace App\Bundle\User\Infrastructure\Action;

use App\Bundle\User\Application\UseCase\Login\LoginDTO;
use App\Bundle\User\Application\UseCase\Login\LoginUseCase;
use App\Bundle\User\Domain\Exception\InvalidCredentialsException;
use App\Bundle\User\Infrastructure\Http\Request\LoginRequest;
use App\Bundle\User\Infrastructure\Http\Response\UserLoggedResponse;
use Illuminate\Http\JsonResponse;

final readonly class LoginAction
{
    public function __construct(
        private LoginUseCase $loginUseCase
    ) {}

    public function __invoke(LoginRequest $loginRequest): JsonResponse
    {
        try {
            $token = $this->loginUseCase->execute(
                loginDTO: new LoginDTO(
                    email: $loginRequest->get(key: 'email'),
                    password: $loginRequest->get(key: 'password')
                )
            );

            return (new UserLoggedResponse(token: $token))->toResponse();

        } catch (InvalidCredentialsException $e) {
            return response()->json(
                data: ['error' => $e->getMessage()],
                status: JsonResponse::HTTP_UNAUTHORIZED
            );
        }
    }
}
