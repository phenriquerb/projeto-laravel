<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaximoTecnicosPorOS implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('A lista de técnicos deve ser um array.');

            return;
        }

        if (count($value) === 0) {
            $fail('É necessário atribuir pelo menos um técnico.');

            return;
        }

        if (count($value) > 3) {
            $fail('Máximo de 3 técnicos por ordem de serviço.');

            return;
        }

        if (count($value) !== count(array_unique($value))) {
            $fail('Não é permitido técnicos duplicados.');

            return;
        }
    }
}
