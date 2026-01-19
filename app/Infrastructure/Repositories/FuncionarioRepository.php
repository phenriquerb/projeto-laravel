<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\FuncionarioRepositoryInterface;
use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Collection;

class FuncionarioRepository implements FuncionarioRepositoryInterface
{
    public function listar(array $filtros = []): Collection
    {
        $query = Funcionario::with('cargo');

        if (isset($filtros['id']) && is_array($filtros['id']) && ! empty($filtros['id'])) {
            $query->whereIn('id', $filtros['id']);
        }

        if (isset($filtros['nome']) && ! empty($filtros['nome'])) {
            $nome = $filtros['nome'] ?? '';
            $query->where('nome', 'like', '%'.$nome.'%');
        }

        if (isset($filtros['ativo']) && is_bool($filtros['ativo'])) {
            $query->where('ativo', $filtros['ativo']);
        }

        return $query->get();
    }
}
