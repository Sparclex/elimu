<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataSampleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_sample', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sample_id')->unsigned();
            $table->bigInteger('data_id')->unsigned();
            $table->string('target');
            $table->string('status')->default('Pending');
            $table->text('additional')->nullable();
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('CASCADE');
            $table->foreign('data_id')->references('id')->on('data')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_sample');
    }
}
