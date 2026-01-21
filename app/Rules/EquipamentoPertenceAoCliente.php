<?php

namespace App\Rules;

use App\Models\Equipamento;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EquipamentoPertenceAoCliente implements ValidationRule
{
    public function __construct(
        private int $clienteId
    ) {}

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

        $equipamento = Equipamento::find((int) $value);

        if (! $equipamento) {
            $fail('O equipamento informado não existe.');

            return;
        }

        if ($equipamento->cliente_id !== $this->clienteId) {
            $fail('O equipamento informado não pertence ao cliente selecionado.');

            return;
        }
    }
}
