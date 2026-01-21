<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Função auxiliar para mapear nome do modelo para nome amigável
        $getResourceName = function (string $modelName): string {
            return match ($modelName) {
                'OrdemServico' => 'Ordem de serviço',
                'Funcionario' => 'Funcionário',
                'Cliente' => 'Cliente',
                'Equipamento' => 'Equipamento',
                default => 'Registro',
            };
        };

        // Handler para erros de validação (422)
        $exceptions->render(function (ValidationException $e, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        });

        // Handler para NotFoundHttpException (404) - Captura Model Binding failures
        $exceptions->render(function (NotFoundHttpException $e, $request) use ($getResourceName) {
            if (! $request->is('api/*')) {
                return null;
            }

            // Verificar se é um erro de Model Binding
            $previous = $e->getPrevious();
            if ($previous instanceof ModelNotFoundException) {
                $modelName = class_basename($previous->getModel());
                $resourceName = $getResourceName($modelName);

                return response()->json([
                    'message' => "{$resourceName} não encontrado(a).",
                ], 404);
            }

            // Retorna erro 404 genérico para outros casos
            return response()->json([
                'message' => 'Recurso não encontrado.',
            ], 404);
        });

        // Handler para ModelNotFoundException direto (caso ocorra)
        $exceptions->render(function (ModelNotFoundException $e, $request) use ($getResourceName) {
            if (! $request->is('api/*')) {
                return null;
            }

            $modelName = class_basename($e->getModel());
            $resourceName = $getResourceName($modelName);

            return response()->json([
                'message' => "{$resourceName} não encontrado(a).",
            ], 404);
        });
    })->create();
