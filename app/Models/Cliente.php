<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'int';

    protected $fillable = [
        'nome',
        'email',
        'cpf_cnpj',
        'whatsapp',
    ];

    /**
     * Relacionamento: Um cliente tem muitos equipamentos
     */
    public function equipamentos(): HasMany
    {
        return $this->hasMany(Equipamento::class);
    }

    /**
     * Relacionamento: Um cliente tem muitas ordens de serviço
     */
    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class);
    }
}
