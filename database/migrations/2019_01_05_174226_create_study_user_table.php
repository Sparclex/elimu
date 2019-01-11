<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_user', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('study_id');
            $table->unsignedInteger('power')->default(10);

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('study_id')->references('id')->on('studies');

            $table->primary(['user_id', 'study_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('study_user');
    }
}
