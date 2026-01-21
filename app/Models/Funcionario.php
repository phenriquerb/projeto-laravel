<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Funcionario extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table = 'funcionarios';

    protected $keyType = 'int';

    protected $fillable = [
        'nome',
        'email',
        'login',
        'password',
        'cargo_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

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
