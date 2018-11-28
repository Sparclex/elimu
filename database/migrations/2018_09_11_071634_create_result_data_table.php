<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('result_id')->unsigned();
            $table->bigInteger('experiment_id')->unsigned();
            $table->string('primary_value')->nullable();
            $table->string('secondary_value')->nullable();
            $table->text('additional')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();


            $table->foreign('result_id')->references('id')->on('results')->onDelete('CASCADE');
            $table->foreign('experiment_id')->references('id')->on('experiments')->onDelete('CASCADE');

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
        Schema::dropIfExists('result_data');
    }
}
