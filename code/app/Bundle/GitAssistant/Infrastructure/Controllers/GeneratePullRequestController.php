<?php

namespace App\Bundle\GitAssistant\Infrastructure\Controllers;

use App\Bundle\GitAssistant\Infrastructure\Action\GeneratePullRequestAction;
use App\Bundle\GitAssistant\Infrastructure\Http\Request\GeneratePullRequestRequest;
use Illuminate\Http\JsonResponse;

final readonly class GeneratePullRequestController
{
    public function __construct(
        private GeneratePullRequestAction $generatePullRequestAction
    ) {}

    /**
     * Generate Pull Request
     *
     * @group GitAssistant
     */
    public function __invoke(GeneratePullRequestRequest $generatePullRequestRequest): JsonResponse
    {
        return ($this->generatePullRequestAction)(generatePullRequestRequest: $generatePullRequestRequest);
    }
}
