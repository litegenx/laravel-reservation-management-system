<?php

use Faker\Generator as Faker;
use App\Models\Room;

/** @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Room::class, function (Faker $faker) {
    return [
        'name'   => $faker->word(),
        'number' => $faker->numberBetween(2, 5),
        'price'  => $faker->numberBetween(10000, 50000),
    ];
});
