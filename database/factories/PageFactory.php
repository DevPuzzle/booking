<?php

use Faker\Generator as Faker;

$factory->define(\App\Page::class, function (Faker $faker) {
    return [
        'title'=>$faker->text($maxNbChars=30),
        'summary'=>$faker->sentence($nbWords = 8),
        'content'=>$faker->paragraph($nbSentences = 7)
    ];
});
