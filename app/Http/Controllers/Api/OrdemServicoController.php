<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\OrdemServicoService;
use App\Application\Services\OrdemServicoPdfService;
use App\Domain\Exceptions\OrdemServicoException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AtribuirTecnicosRequest;
use App\Http\Requests\AtualizarLaudoRequest;
use App\Http\Requests\AtualizarStatusRequest;
use App\Http\Requests\ConcluirOrdemServicoRequest;
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
            $this->ordemServicoService->atualizarStatus($ordemServico, $dados['status']);

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
                $ordemServico,
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

    /**
     * Atualiza o laudo técnico de uma ordem de serviço
     *
     * @return JsonResponse
     */
    public function atualizarLaudo(OrdemServico $ordemServico, AtualizarLaudoRequest $request)
    {
        try {
            $dados = $request->validated();
            $this->ordemServicoService->atualizarLaudo($ordemServico, $dados);

            return response()->json([
                'message' => 'Laudo técnico atualizado com sucesso.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar laudo: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Conclui uma ordem de serviço
     *
     * Marca a OS como concluída, envia email ao cliente com PDF anexo,
     * e registra métricas de receita no Pulse.
     *
     * @return JsonResponse
     */
    public function concluir(OrdemServico $ordemServico, ConcluirOrdemServicoRequest $request)
    {
        try {
            $this->ordemServicoService->concluir($ordemServico);

            return response()->json([
                'message' => 'Ordem de serviço concluída com sucesso. Email enviado ao cliente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao concluir ordem de serviço: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exporta o PDF de uma ordem de serviço
     *
     * @return \Illuminate\Http\Response|JsonResponse
     */
    public function exportarPdf(OrdemServico $ordemServico, OrdemServicoPdfService $pdfService)
    {
        try {
            return $pdfService->streamPdf($ordemServico);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao gerar PDF: '.$e->getMessage(),
            ], 500);
        }
    }
}
