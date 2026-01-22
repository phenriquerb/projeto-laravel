<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FuncionarioController;
use App\Http\Controllers\Api\OrdemServicoController;
use App\Http\Controllers\Api\TestePulseController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (sem autenticação)
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Funcionários
    Route::get('/funcionarios', [FuncionarioController::class, 'index']);

    // Ordem de Serviço
    Route::post('/os', [OrdemServicoController::class, 'store']);
    Route::post('/os/{ordemServico}/evidencias', [OrdemServicoController::class, 'uploadEvidencia']);
    Route::patch('/os/{ordemServico}/status', [OrdemServicoController::class, 'atualizarStatus']);
    Route::post('/os/{ordemServico}/atribuir', [OrdemServicoController::class, 'atribuirTecnicos']);
    Route::put('/os/{ordemServico}/laudo', [OrdemServicoController::class, 'atualizarLaudo']);
    Route::patch('/os/{ordemServico}/concluir', [OrdemServicoController::class, 'concluir']);
    Route::get('/os/{ordemServico}/exportar-pdf', [OrdemServicoController::class, 'exportarPdf']);
});

// Testes Pulse
Route::prefix('teste-pulse')->group(function () {
    Route::get('/teste-lento', [TestePulseController::class, 'testeLento']);
    Route::get('/query-lenta', [TestePulseController::class, 'queryLenta']);
    Route::get('/forcar-erro', [TestePulseController::class, 'forcarErro']);
});
