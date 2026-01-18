<?php

namespace App\Http\Requests;

use App\Rules\CommaSeparatedNumbers;
use Illuminate\Foundation\Http\FormRequest;

class ListarFuncionariosRequest extends FormRequest
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
            'id' => ['nullable', new CommaSeparatedNumbers],
            'nome' => 'nullable|string|max:255',
            'ativo' => 'nullable|string|in:0,1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nome.string' => 'O campo nome deve ser uma string.',
            'nome.max' => 'O campo nome não pode ter mais de 255 caracteres.',
            'ativo.string' => 'O campo ativo deve ser uma string.',
            'ativo.in' => 'O campo ativo deve ser 0 ou 1.',
        ];
    }

    /**
     * Get validated data with converted id to array
     */
    public function getValidatedData(): array
    {
        $data = $this->validated();

        if (isset($data['id']) && is_string($data['id'])) {
            $ids = array_filter(
                array_map('trim', explode(',', $data['id'])),
                fn ($id) => ! empty($id) && is_numeric($id)
            );

            $data['id'] = ! empty($ids) ? array_map('intval', $ids) : null;
        }

        if (isset($data['ativo']) && is_string($data['ativo'])) {
            $data['ativo'] = (bool) (int) $data['ativo'];
        }

        return array_filter($data, fn ($value) => $value !== null);
    }
}
