<?php

namespace App\Providers;

use App\Extensions\SanctumSecurityExtension;
use App\Models\OrdemServico;
use App\Observers\OrdemServicoObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewApiDocs', function ($user = null) {
            return true;
        });

        Gate::define('viewPulse', function ($user = null) {
            return true;
        });

        OrdemServico::observe(OrdemServicoObserver::class);

        // Scramble::extendOpenApi(function (OpenApi $openApi) {
        //     $openApi->secure(
        //         SecurityScheme::http('bearer', 'sanctum')
        //     );
        // });

        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->components->addSecurityScheme(
                'sanctum',
                SecurityScheme::http('bearer')
            );
        });

        Scramble::registerExtension(SanctumSecurityExtension::class);
    }
}
