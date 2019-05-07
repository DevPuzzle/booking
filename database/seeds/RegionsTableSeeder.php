<?php

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new App\Region(['name'=>'Paris']))->save();
        (new App\Region(['name'=>'Toronto']))->save();
        (new App\Region(['name'=>'New York']))->save();
        (new App\Region(['name'=>'Las Vegas']))->save();
    }
}
