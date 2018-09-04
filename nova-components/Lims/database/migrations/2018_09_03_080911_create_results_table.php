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
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->bigInteger('experiment_id')->unsigned();
            $table->bigInteger('data_id')->unsigned();
            $table->timestamps();

            $table->foreign('experiment_id')->references('id')->on('experiments')->onDelete('CASCADE');
            $table->foreign('data_id')->references('id')->on('data')->onDelete('CASCADE');
            $table->primary(['experiment_id', 'data_id']);
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
