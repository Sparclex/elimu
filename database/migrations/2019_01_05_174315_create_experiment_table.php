<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('study_id')->unsigned();
            $table->integer('assay_id')->unsigned();
            $table->timestamp('requested_at')->nullable();
            $table->text('comment')->nullable();

            $table->string('result_file')->nullable();
            $table->string('original_filename')->nullable();
            $table->timestamps();

            $table->foreign('assay_id')->references('id')->on('assays')->onDelete('CASCADE');
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
        Schema::dropIfExists('experiments');
    }
}
