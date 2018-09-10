<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('assay_id')->unsigned();
            $table->bigInteger('processing_log_id')->unsigned();
            $table->timestamps();

            $table->unique(['assay_id', 'processing_log_id']);

            $table->foreign('assay_id')->references('id')->on('assays')->onDelete('CASCADE');
            $table->foreign('processing_log_id')->references('id')->on('processing_logs')->onDelete('CASCADE');
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
