<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\FuncionarioService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListarFuncionariosRequest;
use App\Http\Resources\FuncionarioResource;

class FuncionarioController extends Controller
{
    public function __construct(
        private FuncionarioService $funcionarioService
    ) {}

    /**
     * Lista funcionários com filtros opcionais
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(ListarFuncionariosRequest $request)
    {
        $filtros = $request->getValidatedData();
        $funcionarios = $this->funcionarioService->listar($filtros);

        return FuncionarioResource::collection($funcionarios);
    }
}
