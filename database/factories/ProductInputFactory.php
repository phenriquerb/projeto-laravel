<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use CodeShopping\Models\ProductInput;
use Faker\Generator as Faker;

$factory->define(ProductInput::class, function (Faker $faker) {
    return [
        'amount' => $faker->numberBetween(1,40)
    ];
});
