<?php

use Faker\Generator as Faker;

$factory->define(\App\LeaveDay::class, function (Faker $faker) {
    return [
        'event_id' => str_random($l = 15),
        'html_link' => 'http://calendar.google.com',
        'summary' => $faker->sentence,
        'description' => $faker->sentences(3,true),
        'starts_at' => $faker->dateTimeThisYear('-2 months'),
        'ends_at' => $faker->dateTimeThisYear(),
        'added_by' => 2
    ];
});
