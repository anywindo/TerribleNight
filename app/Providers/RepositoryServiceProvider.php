<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\TeamRepositoryInterface::class,
            \App\Repositories\Eloquent\TeamRepository::class
        );

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
