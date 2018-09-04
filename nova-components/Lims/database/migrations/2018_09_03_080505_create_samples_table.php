<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sample_type_id')->unsigned();
            $table->bigInteger('sample_information_id')->unsigned();
            $table->bigInteger('study_id')->unsigned();
            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->unique(['sample_type_id', 'sample_information_id', 'study_id']);
            $table->foreign('sample_type_id')->references('id')->on('sample_types')->onDelete('CASCADE');
            $table->foreign('sample_information_id')->references('id')->on('sample_informations')->onDelete('CASCADE');
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
        Schema::dropIfExists('samples');
    }
}
