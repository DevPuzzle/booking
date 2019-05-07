<?php

use Illuminate\Database\Seeder;

class LeaveDaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::where('role', 'guide')->get()->each(function($user){
            $user->leaveDays()->saveMany(
                factory(App\LeaveDay::class,3)->make()
            );
        });
    }
}
