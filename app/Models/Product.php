<?php

namespace CodeShopping\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use Sluggable, SoftDeletes;

    protected $dates = ['deleted_at']; // a variavel $dates ja 'e uma variavel imbutida na classe model onde ele ira tratar tudo dentro dela como uma data
    protected $fillable = ['name', 'description', 'price', 'active'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    //many-to-many
    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    //one-to-many
    public function photos(){
        return $this->hasMany(ProductPhoto::class);
    }
}
