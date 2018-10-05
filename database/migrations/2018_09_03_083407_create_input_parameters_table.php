<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInputParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('input_parameters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('study_id')->unsigned();
            $table->bigInteger('assay_id')->unsigned();
            $table->string('name')->nullable()->index();
            $table->string('parameter_file')->nullable();
            $table->json('parameters');
            $table->timestamps();

            $table->foreign('study_id')->references('id')->on('studies')->onDelete('CASCADE');
            $table->foreign('assay_id')->references('id')->on('assays')->onDelete('CASCADE');
            $table->unique(['study_id', 'assay_id', 'name']);
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
