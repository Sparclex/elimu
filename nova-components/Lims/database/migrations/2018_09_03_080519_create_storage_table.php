<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('study_id')->unsigned();
            $table->bigInteger('sample_id')->unsigned();
            $table->bigInteger('sample_type_id')->unsigned();
            $table->integer('box');
            $table->integer('position');
            $table->timestamps();

            $table->unique(['study_id', 'sample_type_id', 'box', 'position']);
            $table->foreign('study_id')->references('id')->on('studies')->onDelete('CASCADE');
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage');
    }
}
