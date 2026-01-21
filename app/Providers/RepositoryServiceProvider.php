<?php

namespace App\Providers;

use App\Domain\Contracts\Repositories\FuncionarioRepositoryInterface;
use App\Domain\Contracts\Repositories\OrdemServicoRepositoryInterface;
use App\Domain\Contracts\Repositories\OsEvidenciaRepositoryInterface;
use App\Infrastructure\Repositories\FuncionarioRepository;
use App\Infrastructure\Repositories\OrdemServicoRepository;
use App\Infrastructure\Repositories\OsEvidenciaRepository;
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

        $this->app->bind(
            OrdemServicoRepositoryInterface::class,
            OrdemServicoRepository::class
        );

        $this->app->bind(
            OsEvidenciaRepositoryInterface::class,
            OsEvidenciaRepository::class
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
