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
     * Busca a última OS com o prefixo especificado (com lock)
     */
    public function buscarUltimaPorPrefixo(string $prefixo): ?OrdemServico;

    /**
     * Atualiza o status de uma ordem de serviço
     */
    public function atualizarStatus(int $id, string $status): OrdemServico;

    /**
     * Atribui técnicos responsáveis a uma ordem de serviço
     */
    public function atribuirTecnicos(int $id, array $funcionariosIds): OrdemServico;
}
