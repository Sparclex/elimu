<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('result_type');

            $table->unsignedInteger('assay_definition_file_id');
            $table->unsignedInteger('instrument_id');
            $table->unsignedInteger('protocol_id');
            $table->unsignedInteger('primer_mix_id');
            $table->timestamps();


            $table->foreign('primer_mix_id')->references('id')->on('primer_mixes');
            $table->foreign('instrument_id')->references('id')->on('instruments');
            $table->foreign('protocol_id')->references('id')->on('protocols');
            $table->foreign('assay_definition_file_id')->references('id')->on('assay_definition_files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assays');
    }
}
