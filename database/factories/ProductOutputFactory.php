<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use CodeShopping\Models\ProductOutput;
use Faker\Generator as Faker;

$factory->define(ProductOutput::class, function (Faker $faker) {
    return [
        'amount' => $faker->numberBetween(1,2)
    ];
});
