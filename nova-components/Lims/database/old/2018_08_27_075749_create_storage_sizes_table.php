<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_sizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('study_id');
            $table->foreign('study_id')->references('id')->on('studies')->onDelete('CASCADE');
            $table->unsignedBigInteger('sample_type_id');
            $table->foreign('sample_type_id')->references('id')->on('sample_types')->onDelete('CASCADE');
            $table->unsignedInteger('size');
            $table->unique(['study_id', 'sample_type_id']);
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
        Schema::dropIfExists('storage_sizes');
    }
}
