<?php

namespace App\Observers;

use App\Domain\Contracts\Repositories\OrdemServicoRepositoryInterface;
use App\Models\OrdemServico;
use Illuminate\Support\Facades\DB;

class OrdemServicoObserver
{
    /**
     * Executa antes de criar uma nova ordem de serviço
     */
    public function creating(OrdemServico $ordemServico): void
    {
        if (empty($ordemServico->protocolo)) {
            $ordemServico->protocolo = $this->gerarProximoProtocolo();
        }
    }

    /**
     * Gera o próximo protocolo no formato YYYYMM-XXX
     */
    private function gerarProximoProtocolo(): string
    {
        $repository = app(OrdemServicoRepositoryInterface::class);

        return DB::transaction(function () use ($repository) {
            $ano = now()->format('Y');
            $mes = now()->format('m');
            $prefixo = "{$ano}{$mes}";

            $ultimaOs = $repository->buscarUltimaPorPrefixo($prefixo);

            if ($ultimaOs) {
                $partes = explode('-', $ultimaOs->protocolo);
                $ultimoNumero = (int) ($partes[1] ?? 0);
                $proximoNumero = $ultimoNumero + 1;
            } else {
                $proximoNumero = 1;
            }

            $numeroFormatado = str_pad($proximoNumero, 3, '0', STR_PAD_LEFT);

            return "{$prefixo}-{$numeroFormatado}";
        });
    }
}
