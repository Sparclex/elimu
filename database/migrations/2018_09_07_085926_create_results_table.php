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
            $table->bigInteger('experiment_id')->unsigned();
            $table->string('target');
            $table->string('value');
            $table->timestamps();

            $table->unique(['sample_id', 'experiment_id', 'target']);
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('CASCADE');
            $table->foreign('experiment_id')->references('id')->on('experiments')->onDelete('CASCADE');
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
