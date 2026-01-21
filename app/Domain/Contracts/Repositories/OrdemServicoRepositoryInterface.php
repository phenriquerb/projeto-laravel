<?php

namespace App\Domain\Contracts\Repositories;

use App\Models\OrdemServico;

interface OrdemServicoRepositoryInterface
{
    /**
     * Cria uma nova ordem de serviço
     */
    public function criar(array $dados): OrdemServico;

    /**
     * Busca uma ordem de serviço por ID com relacionamentos
     */
    public function buscarPorId(int $id): ?OrdemServico;

    /**
     * Carrega as relações de uma ordem de serviço
     */
    public function carregarRelacoes(OrdemServico $ordemServico): OrdemServico;

    /**
     * Busca a última OS com o prefixo especificado (com lock)
     */
    public function buscarUltimaPorPrefixo(string $prefixo): ?OrdemServico;

    /**
     * Atualiza o status de uma ordem de serviço
     */
    public function atualizarStatus(OrdemServico $ordemServico, string $status): void;

    /**
     * Atribui técnicos responsáveis a uma ordem de serviço
     */
    public function atribuirTecnicos(OrdemServico $ordemServico, array $funcionariosIds): void;

    /**
     * Atualiza o laudo técnico de uma ordem de serviço
     */
    public function atualizarLaudo(OrdemServico $ordemServico, array $dados): void;
}
