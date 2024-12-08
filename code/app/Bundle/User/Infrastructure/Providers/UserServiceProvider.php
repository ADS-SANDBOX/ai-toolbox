<?php

namespace App\Bundle\User\Infrastructure\Providers;

use App\Bundle\User\Domain\Repository\UserRepository;
use App\Bundle\User\Infrastructure\Repository\EloquentUserRepository;
use Carbon\Laravel\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(abstract: UserRepository::class, concrete: EloquentUserRepository::class);
    }
}
