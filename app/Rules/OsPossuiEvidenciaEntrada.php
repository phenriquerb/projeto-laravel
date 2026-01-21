<?php

namespace App\Rules;

use App\Models\OrdemServico;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OsPossuiEvidenciaEntrada implements ValidationRule
{
    public function __construct(
        private int $osId
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $temEvidenciaEntrada = OrdemServico::where('id', $this->osId)
            ->whereHas('evidencias', function ($query) {
                $query->where('momento', 'entrada');
            })
            ->exists();

        if (! $temEvidenciaEntrada) {
            $fail('A ordem de serviço precisa ter pelo menos uma evidência de entrada antes de mudar para em análise.');
        }
    }
}
