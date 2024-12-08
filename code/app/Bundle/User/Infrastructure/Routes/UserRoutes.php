<?php

declare(strict_types=1);

use App\Bundle\User\Infrastructure\Controllers\LoginController;
use App\Bundle\User\Infrastructure\Controllers\RegisterController;
use App\Bundle\User\Infrastructure\Controllers\UpdateApiKeyController;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)
    ->name(name: 'register');

Route::post('/login', LoginController::class)
    ->name(name: 'login');

Route::middleware('auth:api')->group(callback: function (): void {
    Route::post('/update-api-key', UpdateApiKeyController::class)
        ->name(name: 'update-api-key');
});
