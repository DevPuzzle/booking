<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new App\Role(['name'=>'Manager', 'slug'=>'mngr']))->save();
        (new App\Role(['name'=>'Guide', 'slug'=>'guide']))->save();
        (new App\Role(['name'=>'Guide', 'slug'=>'guide']))->save();
        (new App\Role(['name'=>'Guide', 'slug'=>'guide']))->save();
        (new App\Role(['name'=>'Administrator', 'slug'=>'administrator']))->save();
    }
}
