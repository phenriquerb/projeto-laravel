<?php

namespace App\Http\Requests;

use App\Enums\StatusOrdemServicoEnum;
use App\Rules\OsPossuiEvidenciaEntrada;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AtualizarStatusRequest extends FormRequest
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
        // Remove o status 'aberta' dos valores aceitos (não pode ser atualizado para aberta)
        $statusPermitidos = collect(StatusOrdemServicoEnum::values())
            ->reject(fn ($status) => $status === StatusOrdemServicoEnum::ABERTA->value)
            ->implode(',');

        return [
            'status' => [
                'required',
                "in:{$statusPermitidos}",
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->status === StatusOrdemServicoEnum::EM_ANALISE->value) {
                $ordemServico = $this->route('ordemServico');

                // Se o Model Binding não resolveu, não validar (404 será retornado)
                if (! $ordemServico) {
                    return;
                }

                $rule = new OsPossuiEvidenciaEntrada($ordemServico->id);

                $rule->validate('status', $this->status, function ($message) use ($validator) {
                    $validator->errors()->add('status', $message);
                });
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser: em_analise, aguardando_pecas, execucao, concluida ou cancelada.',
        ];
    }
}
