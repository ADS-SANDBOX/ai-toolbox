<?php

namespace App\Bundle\User\Infrastructure\Action;

use App\Bundle\User\Application\UseCase\UpdateApiKey\UpdateApiKeyDTO;
use App\Bundle\User\Application\UseCase\UpdateApiKey\UpdateApiKeyUseCase;
use App\Bundle\User\Domain\Exception\UserNotFoundException;
use App\Bundle\User\Infrastructure\Http\Request\UpdateApiKeyRequest;
use App\Bundle\User\Infrastructure\Http\Response\ApiKeyUpdatedResponse;
use Illuminate\Http\JsonResponse;

final readonly class UpdateApiKeyAction
{
    public function __construct(
        private UpdateApiKeyUseCase $updateApiKeyUseCase
    ) {}

    public function __invoke(UpdateApiKeyRequest $updateApiKeyRequest): JsonResponse
    {
        try {
            ($this->updateApiKeyUseCase)(
                updateApiKeyDTO: new UpdateApiKeyDTO(
                    userId: $updateApiKeyRequest->user()->id,
                    apiKey: $updateApiKeyRequest->get(key: 'api_key')
                )
            );

            return (new ApiKeyUpdatedResponse)->toResponse();

        } catch (UserNotFoundException $e) {
            return response()->json(
                data: ['error' => $e->getMessage()],
                status: JsonResponse::HTTP_NOT_FOUND
            );
        }
    }
}
