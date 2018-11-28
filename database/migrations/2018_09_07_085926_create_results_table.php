<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     *
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sample_id')->unsigned();
            $table->bigInteger('assay_id')->unsigned();
            $table->string('target');
            $table->timestamps();

            $table->unique(['sample_id', 'assay_id', 'target']);
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('CASCADE');
            $table->foreign('assay_id')->references('id')->on('assays')->onDelete('CASCADE');

            $table->bigInteger('study_id')->unsigned();
            $table->foreign('study_id')->references('id')->on('studies')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
}
