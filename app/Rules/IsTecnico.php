<?php

namespace App\Rules;

use App\Enums\CargoEnum;
use App\Models\Funcionario;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsTecnico implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value)) {
            $fail('O campo :attribute deve ser um número.');

            return;
        }

        $funcionario = Funcionario::with('cargo')->find((int) $value);

        if (! $funcionario) {
            $fail('O funcionário informado não existe.');

            return;
        }

        if ($funcionario->cargo_id !== CargoEnum::TECNICO->value) {
            $fail('O funcionário informado não possui o cargo de Técnico.');

            return;
        }
    }
}
