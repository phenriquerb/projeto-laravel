<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdemServico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ordens_servico';

    protected $keyType = 'int';

    protected $fillable = [
        'protocolo',
        'cliente_id',
        'equipamento_id',
        'atendente_id',
        'relato_cliente',
        'diagnostico_tecnico',
        'status',
        'prioridade',
        'valor_total',
        'data_conclusao',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'data_conclusao' => 'datetime',
    ];

    /**
     * Relacionamento: Uma OS pertence a um cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relacionamento: Uma OS pertence a um equipamento
     */
    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class);
    }

    /**
     * Relacionamento: Uma OS tem um atendente (funcionário)
     */
    public function atendente(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'atendente_id');
    }

    /**
     * Relacionamento: Uma OS tem muitos responsáveis (funcionários) - Many to Many
     */
    public function responsaveis(): BelongsToMany
    {
        return $this->belongsToMany(
            Funcionario::class,
            'os_responsaveis',
            'ordem_servico_id',
            'funcionario_id'
        )->withTimestamps();
    }

    /**
     * Relacionamento: Uma OS tem muitas evidências (imagens)
     */
    public function evidencias(): HasMany
    {
        return $this->hasMany(OsEvidencia::class, 'ordem_servico_id');
    }
}
