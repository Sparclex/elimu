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
            $table->string('primary_value')->nullable();
            $table->string('secondary_value')->nullable();
            $table->text('additional')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();


            $table->foreign('result_id')->references('id')->on('results')->onDelete('CASCADE');
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
