<?php

namespace App\Bundle\GitAssistant\Infrastructure\Providers;

use App\Bundle\GitAssistant\Domain\Service\CommitGeneratorService;
use App\Bundle\GitAssistant\Domain\Service\PullRequestGeneratorService;
use App\Bundle\GitAssistant\Infrastructure\Service\OpenAICommitGenerator;
use App\Bundle\GitAssistant\Infrastructure\Service\OpenAIPullRequestGenerator;
use Illuminate\Support\ServiceProvider;

class GitAssistantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(abstract: CommitGeneratorService::class, concrete: OpenAICommitGenerator::class);
        $this->app->bind(abstract: PullRequestGeneratorService::class, concrete: OpenAIPullRequestGenerator::class);
    }
}
