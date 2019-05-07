<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarkasreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markasread', function (Blueprint $table) {
            $table->integer('page_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->primary(['user_id', 'page_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('page_id')
                ->references('id')
                ->on('pages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('markasread');
    }
}
