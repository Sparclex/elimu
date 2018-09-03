<?php

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
        Schema::create('storages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('box');
            $table->bigInteger('field');
            $table->unsignedBigInteger('study_id');
            $table->unsignedBigInteger('sample_type_id');
            $table->foreign('study_id')->references('id')->on('studies');
            $table->foreign('sample_type_id')->references('id')->on('sample_types');
            $table->unique(['box', 'field', 'study_id', 'sample_type_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('storage');
    }
}
