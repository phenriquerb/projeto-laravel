<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\OsEvidenciaRepositoryInterface;
use App\Models\OsEvidencia;

class OsEvidenciaRepository implements OsEvidenciaRepositoryInterface
{
    /**
     * Cria uma nova evidência de ordem de serviço
     */
    public function criar(array $dados): OsEvidencia
    {
        return OsEvidencia::create($dados);
    }
}
