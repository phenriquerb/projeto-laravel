<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends Model
{
    use SoftDeletes;

    protected $table = 'cargos';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'nome',
    ];
}
