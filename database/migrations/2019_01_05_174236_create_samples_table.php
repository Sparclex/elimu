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
            $table->increments('id');

            $table->string('sample_id');
            $table->string('subject_id')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->string('visit_id')->nullable();
            $table->integer('study_id')->unsigned();
            $table->date('birthdate')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->json('extra')->nullable();

            $table->timestamps();

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
        Schema::dropIfExists('samples');
    }
}
