<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_informations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sample_id');
            $table->string('subject_id')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->string('visit_id')->nullable();
            $table->bigInteger('study_id')->unsigned();
            $table->date('birthdate')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('sample_informations');
    }
}
