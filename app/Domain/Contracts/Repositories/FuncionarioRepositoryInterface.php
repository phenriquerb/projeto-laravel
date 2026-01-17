<?php

namespace App\Domain\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface FuncionarioRepositoryInterface
{
    /**
     * Lista funcionários com filtros opcionais
     *
     * @param array $filtros
     * @return Collection
     */
    public function listar(array $filtros = []): Collection;
}
