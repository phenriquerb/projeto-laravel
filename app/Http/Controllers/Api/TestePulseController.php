<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TestePulseController extends Controller
{
    /**
     * Endpoint para testar requisições lentas no Pulse
     */
    public function testeLento(): JsonResponse
    {
        sleep(10); // Força um atraso de 10 segundos

        return response()->json(['message' => 'Lento, mas funcionou!']);
    }

    /**
     * Endpoint para testar queries lentas no Pulse
     */
    public function queryLenta(): string
    {
        // Simula uma espera no próprio MySQL
        DB::select('SELECT SLEEP(5)');

        return 'Query registada!';
    }

    /**
     * Endpoint para forçar erros e testar o monitoramento no Pulse
     */
    public function forcarErro(): void
    {
        throw new \Exception('Erro de teste no Pulse!');
    }
}
