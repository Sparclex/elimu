<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorageBoxSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storage_box_sizes', function (Blueprint $table) {
            $table->unsignedInteger('study_id');
            $table->unsignedInteger('sample_type_id');
            $table->integer('rows');
            $table->integer('columns');

            $table->foreign('study_id')->references('id')->on('studies');
            $table->foreign('sample_type_id')->references('id')->on('sample_types');

            $table->primary(['study_id', 'sample_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_box_sizes');
    }
}
