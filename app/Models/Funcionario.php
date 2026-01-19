<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'funcionarios';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'nome',
        'email',
        'ativo',
        'cargo_id',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    /**
     * Relacionamento: Um funcionário pode ser atendente de muitas OS
     */
    public function ordensServicoAtendente()
    {
        return $this->hasMany(OrdemServico::class, 'atendente_id');
    }

    /**
     * Relacionamento: Um funcionário pode ser responsável por muitas OS (Many to Many)
     */
    public function ordensServicoResponsavel()
    {
        return $this->belongsToMany(
            OrdemServico::class,
            'os_responsaveis',
            'funcionario_id',
            'ordem_servico_id'
        )->withTimestamps();
    }
}
