<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\OrdemServicoService;
use App\Domain\Exceptions\OrdemServicoException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AtribuirTecnicosRequest;
use App\Http\Requests\AtualizarStatusRequest;
use App\Http\Requests\CriarOrdemServicoRequest;
use App\Http\Requests\UploadEvidenciaRequest;
use App\Http\Resources\OrdemServicoResource;
use App\Http\Resources\OsEvidenciaResource;
use App\Models\OrdemServico;
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
    public function uploadEvidencia(OrdemServico $ordemServico, UploadEvidenciaRequest $request)
    {
        try {
            $dados = $request->validated();
            $evidencia = $this->ordemServicoService->adicionarEvidencia(
                osId: $ordemServico->id,
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

    /**
     * Atualiza o status de uma ordem de serviço
     *
     * @return JsonResponse
     */
    public function atualizarStatus(OrdemServico $ordemServico, AtualizarStatusRequest $request)
    {
        try {
            $dados = $request->validated();
            $this->ordemServicoService->atualizarStatus($ordemServico->id, $dados['status']);

            return response()->json([
                'message' => 'Status atualizado com sucesso.',
            ], 200);
        } catch (OrdemServicoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar status: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atribui técnicos responsáveis a uma ordem de serviço
     *
     * @return JsonResponse
     */
    public function atribuirTecnicos(OrdemServico $ordemServico, AtribuirTecnicosRequest $request)
    {
        try {
            $dados = $request->validated();
            $this->ordemServicoService->atribuirTecnicos(
                $ordemServico->id,
                $dados['funcionarios_ids']
            );

            return response()->json([
                'message' => 'Técnicos atribuídos com sucesso.',
            ], 200);
        } catch (OrdemServicoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atribuir técnicos: '.$e->getMessage(),
            ], 500);
        }
    }
}
