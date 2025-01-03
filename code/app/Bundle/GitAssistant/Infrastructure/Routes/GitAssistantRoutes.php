<?php

declare(strict_types=1);

use App\Bundle\GitAssistant\Infrastructure\Controllers\GenerateCommitController;
use App\Bundle\GitAssistant\Infrastructure\Controllers\GeneratePullRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(callback: function (): void {
    Route::post('/git-assistant/generate-commit', GenerateCommitController::class)
        ->name(name: 'git-assistant.generate-commit');

    Route::post('/git-assistant/generate-pull-request', GeneratePullRequestController::class)
        ->name(name: 'git-assistant.generate-pull-request');
});
