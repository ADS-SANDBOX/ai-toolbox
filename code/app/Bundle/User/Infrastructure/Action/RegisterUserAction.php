<?php

namespace App\Bundle\User\Infrastructure\Action;

use App\Bundle\User\Application\UseCase\RegisterUser\RegisterUserDTO;
use App\Bundle\User\Application\UseCase\RegisterUser\RegisterUserUseCase;
use App\Bundle\User\Domain\Exception\UserAlreadyExistsException;
use App\Bundle\User\Infrastructure\Http\Request\RegisterUserRequest;
use App\Bundle\User\Infrastructure\Http\Response\UserRegisteredResponse;
use Illuminate\Http\JsonResponse;

final readonly class RegisterUserAction
{
    public function __construct(
        private RegisterUserUseCase $registerUserUseCase
    ) {}

    public function __invoke(RegisterUserRequest $registerUserRequest): JsonResponse
    {
        try {
            $token = $this->registerUserUseCase->execute(
                registerUserDTO: new RegisterUserDTO(
                    name: $registerUserRequest->get(key: 'name'),
                    email: $registerUserRequest->get(key: 'email'),
                    password: $registerUserRequest->get(key: 'password')
                )
            );

            return (new UserRegisteredResponse(token: $token))->toResponse();

        } catch (UserAlreadyExistsException $e) {
            return response()->json(data: [
                'error' => $e->getMessage(),
            ], status: JsonResponse::HTTP_CONFLICT);
        }
    }
}
