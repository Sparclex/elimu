<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrimerMixesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('primer_mixes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->unsignedInteger('reagent_id');
            $table->unsignedInteger('creator_id');
            $table->date('expires_at');
            $table->integer('volume');
            $table->timestamps();

            $table->foreign('reagent_id')->references('id')->on('reagents');
            $table->foreign('creator_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('primer_mixes');
    }
}
