<?php

namespace App\Bundle\GitAssistant\Infrastructure\Controllers;

use App\Bundle\GitAssistant\Infrastructure\Action\GenerateCommitAction;
use App\Bundle\GitAssistant\Infrastructure\Http\Request\GenerateCommitRequest;
use Illuminate\Http\JsonResponse;

final readonly class GenerateCommitController
{
    public function __construct(
        private GenerateCommitAction $generateCommitAction
    ) {}

    /**
     * Generate Commit
     *
     * @group GitAssistant
     */
    public function __invoke(GenerateCommitRequest $generateCommitRequest): JsonResponse
    {
        return ($this->generateCommitAction)(generateCommitRequest: $generateCommitRequest);
    }
}
