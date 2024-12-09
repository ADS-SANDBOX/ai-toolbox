<?php

namespace App\Bundle\User\Infrastructure\Controllers;

use App\Bundle\User\Infrastructure\Action\UpdateApiKeyAction;
use App\Bundle\User\Infrastructure\Http\Request\UpdateApiKeyRequest;
use Illuminate\Http\JsonResponse;

final readonly class UpdateApiKeyController
{
    public function __construct(
        private UpdateApiKeyAction $updateApiKeyAction
    ) {}

    /**
     * Update Api Key
     *
     * @group Profile
     */
    public function __invoke(UpdateApiKeyRequest $updateApiKeyRequest): JsonResponse
    {
        return ($this->updateApiKeyAction)(updateApiKeyRequest: $updateApiKeyRequest);
    }
}
