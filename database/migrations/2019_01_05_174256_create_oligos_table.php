<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOligosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oligos', function (Blueprint $table) {
            $table->increments('id');

            $table->string('oligo_id')->index();
            $table->string('sequence')->index();
            $table->string('5_prime_modification')->nullable();
            $table->string('3_prime_modification')->nullable();
            $table->string('species');
            $table->string('target_gene');
            $table->string('publication');
            $table->text('comment')->nullable();
            $table->unsignedInteger('study_id');
            $table->timestamps();

            $table->unique(['oligo_id', 'study_id']);

            $table->foreign('study_id')->references('id')->on('studies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oligos');
    }
}
