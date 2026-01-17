<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CommaSeparatedNumbers implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Se for null, deixa outras regras (nullable) tratarem
        if ($value === null) {
            return;
        }

        // Deve ser uma string
        if (!is_string($value)) {
            $fail('O campo :attribute deve ser uma string.');
            return;
        }

        // Remove espaços em branco para validar
        $trimmed = trim($value);
        
        // Se estiver vazio após trim, não é válido
        if (empty($trimmed)) {
            $fail('O campo :attribute não pode estar vazio.');
            return;
        }

        // Divide por vírgula e valida cada parte
        $parts = explode(',', $trimmed);
        
        foreach ($parts as $part) {
            $part = trim($part);
            
            // Cada parte não pode estar vazia (evita vírgulas consecutivas ou espaços)
            if (empty($part)) {
                $fail('O campo :attribute contém vírgulas consecutivas ou espaços inválidos.');
                return;
            }
            
            // Cada parte deve ser um número inteiro válido (apenas dígitos 0-9)
            // ctype_digit verifica se todos os caracteres são dígitos decimais
            if (!ctype_digit($part)) {
                $fail('O campo :attribute deve conter apenas números inteiros positivos separados por vírgula.');
                return;
            }
        }
    }
}
