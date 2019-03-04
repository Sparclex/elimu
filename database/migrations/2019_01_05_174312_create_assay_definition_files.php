<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssayDefinitionFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assay_definition_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('original_name');
            $table->string('path');
            $table->json('parameters');
            $table->unsignedInteger('sample_type_id');
            $table->string('result_type');
            $table->unsignedInteger('study_id');
            $table->timestamps();

            $table->foreign('study_id')->references('id')->on('studies');
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
        Schema::dropIfExists('assay_definition_files');
    }
}
