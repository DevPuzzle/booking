<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->integer('role_id')->nullable();
            $table->integer('region_id')->nullable();
        });

        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            ['name'=>'Manager', 'slug'=>'manager'],
            ['name'=>'Administrator', 'slug'=>'administrator'],
            ['name'=>'Guide', 'slug'=>'guide']
        ]);

        DB::table('regions')->truncate();

        DB::table('regions')->insert([
            ['name'=>'Paris', 'slug'=>'paris'],
            ['name'=>'Toronto', 'slug'=>'toronto']
        ]);

        $regions_id = DB::table('regions')->select('id')->pluck('id')->all();

        DB::table('users')->get()->map(function($user) use ($regions_id){
            if($user->role == 'mngr'){
                $role_id = DB::table('roles')->where('slug','manager')->get()->first()->id;
            } else {
                $role_id = DB::table('roles')->where('slug',$user->role)->get()->first()->id;
            }
            $key = array_rand($regions_id, 1);
            DB::table('users')->where('id', $user->id)->update(['role_id' => $role_id, 'region_id' => $regions_id[$key]]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
