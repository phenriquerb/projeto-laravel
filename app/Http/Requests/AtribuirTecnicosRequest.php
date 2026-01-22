<?php

namespace App\Http\Requests;

use App\Rules\IsTecnico;
use App\Rules\MaximoTecnicosPorOS;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AtribuirTecnicosRequest extends FormRequest
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
            'funcionarios_ids' => [
                'required',
                'array',
                new MaximoTecnicosPorOS,
            ],
            'funcionarios_ids.*' => [
                'required',
                'integer',
                new IsTecnico,
            ],
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

            // Validar imutabilidade
            if (in_array($ordemServico->status, ['concluida', 'cancelada'])) {
                $validator->errors()->add(
                    'status',
                    'Esta OS já foi concluída ou cancelada e não pode ter técnicos atribuídos.'
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'funcionarios_ids.required' => 'A lista de técnicos é obrigatória.',
            'funcionarios_ids.array' => 'A lista de técnicos deve ser um array.',
            'funcionarios_ids.*.required' => 'Todos os técnicos devem ser informados.',
            'funcionarios_ids.*.integer' => 'Os IDs dos técnicos devem ser números válidos.',
        ];
    }
}
