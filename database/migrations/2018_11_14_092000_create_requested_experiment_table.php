<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestedExperimentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requested_experiments', function (Blueprint $table) {
            $table->bigInteger('experiment_id')->unsigned();
            $table->bigInteger('sample_id')->unsigned();
            $table->timestamps();

            $table->foreign('experiment_id')->references('id')->on('experiments')->onDelete('CASCADE');
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requested_experiments');
    }
}
