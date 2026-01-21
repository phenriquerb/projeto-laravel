<?php

use App\Http\Controllers\Api\FuncionarioController;
use App\Http\Controllers\Api\OrdemServicoController;
use App\Http\Controllers\Api\TestePulseController;
use Illuminate\Support\Facades\Route;

Route::get('/funcionarios', [FuncionarioController::class, 'index']);

// Ordem de Serviço
Route::post('/os', [OrdemServicoController::class, 'store']);
Route::post('/os/{ordemServico}/evidencias', [OrdemServicoController::class, 'uploadEvidencia']);
Route::patch('/os/{ordemServico}/status', [OrdemServicoController::class, 'atualizarStatus']);
Route::post('/os/{ordemServico}/atribuir', [OrdemServicoController::class, 'atribuirTecnicos']);

Route::prefix('teste-pulse')->group(function () {
    Route::get('/teste-lento', [TestePulseController::class, 'testeLento']);
    Route::get('/query-lenta', [TestePulseController::class, 'queryLenta']);
    Route::get('/forcar-erro', [TestePulseController::class, 'forcarErro']);
});
