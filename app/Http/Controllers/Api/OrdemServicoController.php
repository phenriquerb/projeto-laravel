<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\OrdemServicoService;
use App\Domain\Exceptions\OrdemServicoException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CriarOrdemServicoRequest;
use App\Http\Requests\UploadEvidenciaRequest;
use App\Http\Resources\OrdemServicoResource;
use App\Http\Resources\OsEvidenciaResource;
use Illuminate\Http\JsonResponse;

class OrdemServicoController extends Controller
{
    public function __construct(
        private OrdemServicoService $ordemServicoService
    ) {}

    /**
     * Cria uma nova ordem de serviço
     *
     * @return OrdemServicoResource|JsonResponse
     */
    public function store(CriarOrdemServicoRequest $request)
    {
        try {
            $dados = $request->validated();
            $os = $this->ordemServicoService->criar(dados: $dados);

            return (new OrdemServicoResource($os))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar ordem de serviço: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Faz upload de evidência (imagem) para uma OS
     *
     * @return OsEvidenciaResource|JsonResponse
     */
    public function uploadEvidencia(int $id, UploadEvidenciaRequest $request)
    {
        try {
            $dados = $request->validated();
            $evidencia = $this->ordemServicoService->adicionarEvidencia(
                osId: $id,
                imagem: $dados['imagem'],
                legenda: $dados['legenda'] ?? null
            );

            return (new OsEvidenciaResource($evidencia))
                ->response()
                ->setStatusCode(201);
        } catch (OrdemServicoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao fazer upload da evidência: '.$e->getMessage(),
            ], 500);
        }
    }
}
