<?php

namespace App\Providers;

use App\Domain\Contracts\Repositories\FuncionarioRepositoryInterface;
use App\Infrastructure\Repositories\FuncionarioRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            FuncionarioRepositoryInterface::class,
            FuncionarioRepository::class
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
