<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use SoftDeletes;

    protected $table = 'funcionarios';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'nome',
        'cargo_id',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }
}
