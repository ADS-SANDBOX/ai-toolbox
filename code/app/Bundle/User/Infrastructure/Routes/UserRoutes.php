<?php

declare(strict_types=1);

use App\Bundle\User\Infrastructure\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class)
    ->name(name: 'register');
