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

            $table->string('oligo_id')->unique();
            $table->string('sequence')->index();
            $table->string('5_min_modification')->nullable();
            $table->string('3_min_modification')->nullable();
            $table->string('species');
            $table->string('target_gene');
            $table->string('concentration');
            $table->string('publication');
            $table->text('comment')->nullable();
            $table->timestamps();
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
