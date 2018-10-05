<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('study_id')->unsigned()->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('Scientist');
            $table->string('timezone');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('study_id')->references('id')->on('studies')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
