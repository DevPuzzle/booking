<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_days', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event_id')->nullable()->unique();
            $table->string('read_id')->nullable()->unique();
            $table->string('write_id')->nullable()->unique();
            $table->string('html_link')->nullable();
            $table->string('summary')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->text('recurrence')->nullable();
            $table->timestamp('recurring_last_instance_date')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('added_by')->nullable();
            $table->timestamps();
            $table->timestamp('write_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_days');
    }
}
