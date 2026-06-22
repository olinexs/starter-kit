<?php

namespace Modules\Auth\app\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\app\Repositories\AuthRepositoryInterface;
use Modules\Auth\app\Repositories\EloquentAuthRepository;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthRepositoryInterface::class,
            EloquentAuthRepository::class,
        );
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }
}
