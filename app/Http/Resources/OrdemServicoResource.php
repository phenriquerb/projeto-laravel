<?php

namespace App\Http\Resources;

use App\Models\OrdemServico;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdemServicoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var OrdemServico $this */
        return [
            'id' => $this->id,
            'protocolo' => $this->protocolo,
            'status' => $this->status,
            'prioridade' => $this->prioridade,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'equipamento' => new EquipamentoResource($this->whenLoaded('equipamento')),
            'atendente' => new FuncionarioResource($this->whenLoaded('atendente')),
            'relato_cliente' => $this->relato_cliente,
            'diagnostico_tecnico' => $this->diagnostico_tecnico,
            'valor_total' => $this->valor_total,
            'data_conclusao' => $this->data_conclusao?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
