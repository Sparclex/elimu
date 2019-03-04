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
            $table->increments('id');
            $table->integer('study_id')->unsigned();
            $table->integer('sample_id')->unsigned();
            $table->integer('sample_type_id')->unsigned();
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->unique(['study_id', 'sample_type_id', 'position']);
            $table->foreign('study_id')->references('id')->on('studies')->onDelete('CASCADE');
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('CASCADE');
            $table->foreign('sample_type_id')->references('id')->on('sample_types')->onDelete('CASCADE');
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
