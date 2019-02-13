<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
$factory->define(App\Want::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'description' => $faker->paragraph(),
        'user_id' => $faker->unique()->safeEmail,
        'cost' => $faker->numberBetween(100,2000), // secret
        'status' => 1,
        'category_id' => $faker->numberBetween(1,3),

    ];
});
