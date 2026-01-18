<?php

namespace App\Http\Responses;

use App\Models\Funcionario;
use Illuminate\Database\Eloquent\Collection;

class FuncionarioResponse
{
    /**
     * Formata uma coleção de funcionários para resposta
     */
    public static function formatCollection(Collection $funcionarios): array
    {
        return $funcionarios->map(function (Funcionario $funcionario) {
            return self::formatItem($funcionario);
        })->toArray();
    }

    /**
     * Formata um funcionário para resposta
     */
    public static function formatItem(Funcionario $funcionario): array
    {
        return [
            'id' => $funcionario->id,
            'nome' => $funcionario->nome,
            'cargo' => $funcionario->cargo ? [
                'id' => $funcionario->cargo->id,
                'nome' => $funcionario->cargo->nome,
            ] : null,
        ];
    }
}
