<?php

namespace App\Domain\Contracts\Repositories;

use App\Models\OsEvidencia;

interface OsEvidenciaRepositoryInterface
{
    /**
     * Cria uma nova evidência de ordem de serviço
     */
    public function criar(array $dados): OsEvidencia;
}
