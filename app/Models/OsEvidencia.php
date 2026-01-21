<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OsEvidencia extends Model
{
    use HasFactory;

    protected $keyType = 'int';

    protected $fillable = [
        'ordem_servico_id',
        'path',
        'legenda',
        'momento',
    ];

    /**
     * Relacionamento: Uma evidência pertence a uma ordem de serviço
     */
    public function ordemServico(): BelongsTo
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }
}
