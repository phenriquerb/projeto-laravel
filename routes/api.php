<?php

use App\Http\Controllers\Api\FuncionarioController;
use Illuminate\Support\Facades\Route;

Route::get('/funcionarios', [FuncionarioController::class, 'index']);

// ---------- endpoinsts para testar o pulse -----------

Route::get('/teste-lento', function () {
    sleep(10); // Força um atraso de 2 segundos

    return response()->json(['message' => 'Lento, mas funcionou!']);
});

Route::get('/query-lenta', function () {
    // Simula uma espera no próprio MySQL
    DB::select('SELECT SLEEP(5)');

    return 'Query registada!';
});

Route::get('/forcar-erro', function () {
    throw new \Exception('Erro de teste no Pulse!');
});
