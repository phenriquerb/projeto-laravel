<?php

namespace App\Http\Requests;

use App\Rules\TecnicoResponsavelPelaOS;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ConcluirOrdemServicoRequest extends FormRequest
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
        return [];
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

            if (strlen($ordemServico->diagnostico_tecnico ?? '') < 50) {
                $validator->errors()->add(
                    'diagnostico_tecnico',
                    'O diagnóstico técnico deve ter no mínimo 50 caracteres para concluir a OS.'
                );
            }

            if (in_array($ordemServico->status, ['concluida', 'cancelada'])) {
                $validator->errors()->add(
                    'status',
                    'Esta OS já foi concluída ou cancelada e não pode ser alterada.'
                );
            }

            $funcionarioId = $this->user()->id;
            $rule = new TecnicoResponsavelPelaOS($funcionarioId, $ordemServico->id);

            $rule->validate('tecnico', null, function ($message) use ($validator) {
                $validator->errors()->add('tecnico', $message);
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
            'diagnostico_tecnico' => 'O diagnóstico técnico deve ter no mínimo 50 caracteres para concluir a OS.',
            'status' => 'Esta OS já foi concluída ou cancelada e não pode ser alterada.',
            'tecnico' => 'Você não tem permissão para concluir esta ordem de serviço.',
        ];
    }
}
