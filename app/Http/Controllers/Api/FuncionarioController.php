<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\FuncionarioService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListarFuncionariosRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\FuncionarioResponse;

class FuncionarioController extends Controller
{
    public function __construct(
        private FuncionarioService $funcionarioService
    ) {
    }

    /**
     * Lista funcionários com filtros opcionais
     *
     * @param ListarFuncionariosRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListarFuncionariosRequest $request)
    {
        try {
            $filtros = $request->getValidatedData();
            $funcionarios = $this->funcionarioService->listar($filtros);
            $data = FuncionarioResponse::formatCollection($funcionarios);

            return ApiResponse::success($data);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
