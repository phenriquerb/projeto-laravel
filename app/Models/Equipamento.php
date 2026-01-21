<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipamento extends Model
{
    use HasFactory;

    protected $keyType = 'int';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'marca',
        'modelo',
        'numero_serie',
    ];

    /**
     * Relacionamento: Um equipamento pertence a um cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relacionamento: Um equipamento tem muitas ordens de serviço
     */
    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class);
    }
}
