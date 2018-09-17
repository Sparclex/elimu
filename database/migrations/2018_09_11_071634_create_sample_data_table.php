<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sample_id')->unsigned();
            $table->bigInteger('experiment_id')->unsigned();
            $table->string('primary_value')->nullable();
            $table->string('secondary_value')->nullable();
            $table->string('target');
            $table->string('status')->default('Pending');
            $table->text('additional')->nullable();

            $table->timestamps();
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
        Schema::dropIfExists('sample_data');
    }
}
