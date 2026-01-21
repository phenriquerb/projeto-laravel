<?php

namespace App\Http\Resources;

use App\Models\OsEvidencia;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OsEvidenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var OsEvidencia $this */
        return [
            'id' => $this->id,
            'ordem_servico_id' => $this->ordem_servico_id,
            'path' => $this->path,
            'url' => Storage::disk('public')->url($this->path),
            'legenda' => $this->legenda,
            'momento' => $this->momento,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
