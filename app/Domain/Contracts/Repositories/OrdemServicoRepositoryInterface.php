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
     * Busca uma ordem de serviço por ID
     */
    public function buscarPorId(int $id): ?OrdemServico;

    /**
     * Busca a última OS com o prefixo especificado (com lock)
     */
    public function buscarUltimaPorPrefixo(string $prefixo): ?OrdemServico;
}
