<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Retorna resposta de sucesso
     */
    public static function success(mixed $data, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Retorna resposta de erro
     */
    public static function error(mixed $data, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => $data,
        ], $statusCode);
    }
}
