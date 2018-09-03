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
            $table->bigInteger('test_id')->unsigned();
            $table->bigInteger('data_id')->unsigned();
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('tests')->onDelete('CASCADE');
            $table->foreign('data_id')->references('id')->on('data')->onDelete('CASCADE');
            $table->primary(['test_id', 'data_id']);
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
