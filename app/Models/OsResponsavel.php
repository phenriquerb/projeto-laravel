<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OsResponsavel extends Model
{
    use HasFactory;

    protected $table = 'os_responsaveis';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'ordem_servico_id',
        'funcionario_id',
    ];

    /**
     * Relacionamento: Um registro pertence a uma ordem de serviço
     */
    public function ordemServico(): BelongsTo
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }

    /**
     * Relacionamento: Um registro pertence a um funcionário
     */
    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }
}
