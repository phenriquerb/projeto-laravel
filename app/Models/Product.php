<?php

namespace CodeShopping\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Sluggable;
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
