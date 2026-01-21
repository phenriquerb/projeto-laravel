<?php

namespace App\Application\Services;

use App\Domain\Contracts\Repositories\OrdemServicoRepositoryInterface;
use App\Domain\Contracts\Repositories\OsEvidenciaRepositoryInterface;
use App\Jobs\EnviarEmailOrdemServicoAberta;
use App\Models\OrdemServico;
use App\Models\OsEvidencia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Facades\Pulse;

class OrdemServicoService
{
    public function __construct(
        private OrdemServicoRepositoryInterface $ordemServicoRepository,
        private OsEvidenciaRepositoryInterface $osEvidenciaRepository
    ) {}

    /**
     * Cria uma nova ordem de serviço
     */
    public function criar(array $dados): OrdemServico
    {
        return DB::transaction(function () use ($dados) {
            $dadosOs = [
                'cliente_id' => $dados['cliente_id'],
                'equipamento_id' => $dados['equipamento_id'],
                'atendente_id' => $dados['atendente_id'],
                'relato_cliente' => $dados['relato_cliente'],
                'prioridade' => $dados['prioridade'],
                'status' => 'aberta',
            ];

            $os = $this->ordemServicoRepository->criar($dadosOs);

            EnviarEmailOrdemServicoAberta::dispatch($os->id)->afterCommit();

            DB::afterCommit(function () use ($os) {
                Pulse::record('os.aberta', $os->id, 1, now());
            });

            return $this->ordemServicoRepository->carregarRelacoes($os);
        });
    }

    /**
     * Adiciona uma evidência (imagem) à ordem de serviço
     */
    public function adicionarEvidencia(int $osId, UploadedFile $imagem, ?string $legenda = null): OsEvidencia
    {
        $ano = now()->format('Y');
        $mes = now()->format('m');
        $path = "evidencias/{$ano}/{$mes}";

        $nomeArquivo = $imagem->hashName();
        $caminhoCompleto = $imagem->storeAs($path, $nomeArquivo, 'public');

        $evidencia = $this->osEvidenciaRepository->criar([
            'ordem_servico_id' => $osId,
            'path' => $caminhoCompleto,
            'legenda' => $legenda,
            'momento' => 'entrada',
        ]);

        return $evidencia;
    }

    /**
     * Atualiza o status de uma ordem de serviço
     */
    public function atualizarStatus(int $id, string $novoStatus): void
    {
        DB::transaction(function () use ($id, $novoStatus) {
            $os = $this->ordemServicoRepository->atualizarStatus($id, $novoStatus);

            // Registrar métrica no Pulse quando OS entra em análise
            if ($novoStatus === 'em_analise') {
                DB::afterCommit(function () use ($os) {
                    $tempoEspera = now()->diffInMinutes($os->created_at);
                    Pulse::record('os.tempo_espera', $tempoEspera);
                });
            }
        });
    }

    /**
     * Atribui técnicos responsáveis a uma ordem de serviço
     */
    public function atribuirTecnicos(int $id, array $funcionariosIds): void
    {
        DB::transaction(function () use ($id, $funcionariosIds) {
            $os = $this->ordemServicoRepository->atribuirTecnicos($id, $funcionariosIds);

            // Disparar evento para WebSocket após o commit
            DB::afterCommit(function () use ($os, $funcionariosIds) {
                event(new \App\Events\OsTecnicoAtribuido($os, $funcionariosIds));
            });
        });
    }
}
