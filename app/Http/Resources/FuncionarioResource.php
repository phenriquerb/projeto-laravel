<?php

namespace App\Http\Resources;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FuncionarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Funcionario $this */
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'ativo' => (bool) $this->ativo,
            'cargo' => $this->whenLoaded('cargo', fn () => new CargoResource($this->cargo)),
        ];
    }
}
