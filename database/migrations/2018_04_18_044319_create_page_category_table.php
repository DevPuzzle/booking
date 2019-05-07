<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_category', function (Blueprint $table) {
            $table->integer('page_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->primary(['page_id', 'category_id']);
            $table->foreign('page_id')
                ->references('id')
                ->on('pages');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_category');
    }
}
