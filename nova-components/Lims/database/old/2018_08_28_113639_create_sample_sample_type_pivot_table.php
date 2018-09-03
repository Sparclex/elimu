<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleSampleTypePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_places', function (Blueprint $table) {
            $table->bigInteger('sample_type_id')->unsigned()->index();
            $table->foreign('sample_type_id')->references('id')->on('sample_types')->onDelete('cascade');
            $table->bigInteger('sample_id')->unsigned()->index();
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('cascade');
            $table->bigInteger('storage_id')->unsigned()->nullable();
            $table->foreign('storage_id')->references('id')->on('storages')->onDelete('SET NULL');
            $table->primary(['sample_type_id', 'sample_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('storage_places');
    }
}
