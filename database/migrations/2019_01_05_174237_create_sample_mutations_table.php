<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_mutations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sample_type_id')->unsigned();
            $table->integer('sample_id')->unsigned();
            $table->integer('study_id')->unsigned();
            $table->integer('quantity')->default(0);
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->unique(['sample_type_id', 'sample_id']);
            $table->foreign('sample_type_id')->references('id')->on('sample_types')->onDelete('CASCADE');
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('CASCADE');

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
        Schema::dropIfExists('sample_mutations');
    }
}
