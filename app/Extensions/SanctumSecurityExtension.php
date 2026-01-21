<?php

namespace App\Extensions;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\SecurityRequirement;
use Dedoc\Scramble\Support\RouteInfo;

class SanctumSecurityExtension extends OperationExtension
{
    public function handle(Operation $operation, RouteInfo $routeInfo)
    {
        $middlewares = $routeInfo->route->gatherMiddleware();

        $hasAuthSanctum = collect($middlewares)->contains(function ($middleware) {
            return is_string($middleware) && str_starts_with($middleware, 'auth:sanctum');
        });

        if ($hasAuthSanctum) {
            $operation->addSecurity(new SecurityRequirement(['sanctum' => []]));
        }
    }
}
