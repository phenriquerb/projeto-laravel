<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use CodeShopping\Models\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName
        //'slug' => 'slug'
    ];
});
