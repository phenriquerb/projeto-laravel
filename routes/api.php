<?php

use App\Http\Controllers\Api\FuncionarioController;
use Illuminate\Support\Facades\Route;

Route::get('/funcionarios', [FuncionarioController::class, 'index']);
