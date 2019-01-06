<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOligoPrimerMixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oligo_primer_mix', function (Blueprint $table) {
            $table->unsignedInteger('oligo_id');
            $table->unsignedInteger('primer_mix_id');
            $table->integer('concentration')->default(100);

            $table->foreign('oligo_id')->references('id')->on('oligos');
            $table->foreign('primer_mix_id')->references('id')->on('primer_mixes');

            $table->primary(['oligo_id', 'primer_mix_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oligo_primer_mix');
    }
}
