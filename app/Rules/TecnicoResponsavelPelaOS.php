<?php

namespace App\Rules;

use App\Models\OrdemServico;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TecnicoResponsavelPelaOS implements ValidationRule
{
    public function __construct(
        private int $funcionarioId,
        private int $osId
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isTecnicoResponsavel = OrdemServico::where('id', $this->osId)
            ->whereHas('responsaveis', function ($query) {
                $query->where('funcionario_id', $this->funcionarioId);
            })
            ->exists();

        if (! $isTecnicoResponsavel) {
            $fail('Você não tem permissão para editar o laudo técnico desta ordem de serviço.');
        }
    }
}
