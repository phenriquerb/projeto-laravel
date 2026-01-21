<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\OrdemServicoRepositoryInterface;
use App\Models\OrdemServico;

class OrdemServicoRepository implements OrdemServicoRepositoryInterface
{
    /**
     * Cria uma nova ordem de serviço
     */
    public function criar(array $dados): OrdemServico
    {
        return OrdemServico::create($dados);
    }

    /**
     * Busca uma ordem de serviço por ID com relacionamentos
     */
    public function buscarPorId(int $id): ?OrdemServico
    {
        return OrdemServico::with(['cliente', 'equipamento', 'atendente.cargo'])
            ->find($id);
    }

    /**
     * Carrega as relações de uma ordem de serviço
     */
    public function carregarRelacoes(OrdemServico $ordemServico): OrdemServico
    {
        return $ordemServico->load(['cliente', 'equipamento', 'atendente.cargo']);
    }

    /**
     * Busca a última OS com o prefixo especificado (com lock)
     */
    public function buscarUltimaPorPrefixo(string $prefixo): ?OrdemServico
    {
        return OrdemServico::where('protocolo', 'like', "{$prefixo}-%")
            ->orderBy('protocolo', 'desc')
            ->lockForUpdate()
            ->first();
    }

    /**
     * Atualiza o status de uma ordem de serviço
     */
    public function atualizarStatus(int $id, string $status): OrdemServico
    {
        $os = $this->buscarPorId($id);
        $os->update(['status' => $status]);

        return $os->fresh();
    }

    /**
     * Atribui técnicos responsáveis a uma ordem de serviço
     */
    public function atribuirTecnicos(int $id, array $funcionariosIds): OrdemServico
    {
        $os = $this->buscarPorId($id);
        $os->responsaveis()->sync($funcionariosIds);

        return $os->load('responsaveis.cargo');
    }
}
