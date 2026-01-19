<?php

use App\Http\Controllers\Api\FuncionarioController;
use App\Http\Controllers\Api\TestePulseController;
use Illuminate\Support\Facades\Route;

Route::get('/funcionarios', [FuncionarioController::class, 'index']);

Route::prefix('teste-pulse')->group(function () {
    Route::get('/teste-lento', [TestePulseController::class, 'testeLento']);
    Route::get('/query-lenta', [TestePulseController::class, 'queryLenta']);
    Route::get('/forcar-erro', [TestePulseController::class, 'forcarErro']);
});
