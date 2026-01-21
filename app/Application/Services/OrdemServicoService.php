<?php

namespace App\Application\Services;

use App\Domain\Contracts\Repositories\OrdemServicoRepositoryInterface;
use App\Domain\Exceptions\OrdemServicoException;
use App\Jobs\EnviarEmailOrdemServicoAberta;
use App\Models\OrdemServico;
use App\Models\OsEvidencia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Facades\Pulse;

class OrdemServicoService
{
    public function __construct(
        private OrdemServicoRepositoryInterface $ordemServicoRepository
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

            return $this->ordemServicoRepository->buscarPorId($os->id);
        });
    }

    /**
     * Adiciona uma evidência (imagem) à ordem de serviço
     */
    public function adicionarEvidencia(int $osId, UploadedFile $imagem, ?string $legenda = null): OsEvidencia
    {
        $os = $this->ordemServicoRepository->buscarPorId($osId);

        if (! $os) {
            throw new OrdemServicoException('Ordem de serviço não encontrada.');
        }

        $ano = now()->format('Y');
        $mes = now()->format('m');
        $path = "evidencias/{$ano}/{$mes}";

        $nomeArquivo = $imagem->hashName();
        $caminhoCompleto = $imagem->storeAs($path, $nomeArquivo, 'public');

        $evidencia = OsEvidencia::create([
            'ordem_servico_id' => $osId,
            'path' => $caminhoCompleto,
            'legenda' => $legenda,
            'momento' => 'entrada',
        ]);

        return $evidencia;
    }
}
