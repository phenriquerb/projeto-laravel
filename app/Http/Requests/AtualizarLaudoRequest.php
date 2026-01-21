<?php

namespace App\Http\Requests;

use App\Rules\TecnicoResponsavelPelaOS;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AtualizarLaudoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'diagnostico_tecnico' => ['required', 'string', 'min:10'],
            'valor_total' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $ordemServico = $this->route('ordemServico');

            if (! $ordemServico) {
                return;
            }

            $funcionarioId = $this->user()->id;

            $rule = new TecnicoResponsavelPelaOS($funcionarioId, $ordemServico->id);

            $rule->validate('diagnostico_tecnico', $this->diagnostico_tecnico, function ($message) use ($validator) {
                $validator->errors()->add('diagnostico_tecnico', $message);
            });
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'diagnostico_tecnico.required' => 'O diagnóstico técnico é obrigatório.',
            'diagnostico_tecnico.string' => 'O diagnóstico técnico deve ser uma string.',
            'diagnostico_tecnico.min' => 'O diagnóstico técnico deve ter no mínimo 10 caracteres.',
            'valor_total.required' => 'O valor total é obrigatório.',
            'valor_total.numeric' => 'O valor total deve ser um número.',
            'valor_total.min' => 'O valor total deve ser maior que zero.',
        ];
    }
}
