<?php

namespace App\Http\Requests;

use App\Rules\EquipamentoPertenceAoCliente;
use App\Rules\IsAtendente;
use Illuminate\Foundation\Http\FormRequest;

class CriarOrdemServicoRequest extends FormRequest
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
            'cliente_id' => ['required', 'integer', 'exists:clientes,id,deleted_at,NULL'],
            'equipamento_id' => [
                'required',
                'integer',
                'exists:equipamentos,id',
                new EquipamentoPertenceAoCliente($this->input('cliente_id', 0)),
            ],
            'atendente_id' => ['required', 'integer', new IsAtendente],
            'relato_cliente' => ['required', 'string', 'min:10'],
            'prioridade' => ['required', 'in:baixa,media,alta,critica'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cliente_id.required' => 'O cliente é obrigatório.',
            'cliente_id.integer' => 'O cliente deve ser um número válido.',
            'cliente_id.exists' => 'O cliente informado não existe ou está inativo.',
            'equipamento_id.required' => 'O equipamento é obrigatório.',
            'equipamento_id.integer' => 'O equipamento deve ser um número válido.',
            'equipamento_id.exists' => 'O equipamento informado não existe.',
            'equipamento_id.equipamento_pertence_ao_cliente' => 'O equipamento informado não pertence ao cliente selecionado.',
            'atendente_id.required' => 'O atendente é obrigatório.',
            'atendente_id.integer' => 'O atendente deve ser um número válido.',
            'relato_cliente.required' => 'O relato do cliente é obrigatório.',
            'relato_cliente.min' => 'O relato do cliente deve ter no mínimo 10 caracteres.',
            'prioridade.required' => 'A prioridade é obrigatória.',
            'prioridade.in' => 'A prioridade deve ser: baixa, media, alta ou critica.',
        ];
    }
}
