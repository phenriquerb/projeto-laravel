<?php

namespace App\Application\Services;

use App\Domain\Contracts\Repositories\FuncionarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FuncionarioService
{
    public function __construct(
        private FuncionarioRepositoryInterface $funcionarioRepository
    ) {}

    /**
     * Lista funcionários com filtros
     */
    public function listar(array $filtros = []): Collection
    {
        return $this->funcionarioRepository->listar($filtros);
    }
}
