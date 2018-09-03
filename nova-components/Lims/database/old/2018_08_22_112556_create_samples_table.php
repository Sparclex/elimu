<?php

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
            $table->bigInteger('brady_number')->index();
            $table->string('subject_id')->index();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->string('visit');
            $table->json('additional')->nullable();
            $table->string('condition')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('study_id');
            $table->foreign('study_id')->references('id')->on('studies');
            $table->unsignedBigInteger('deliverer_id');
            $table->foreign('deliverer_id')->references('id')->on('people');
            $table->unsignedBigInteger('receiver_id');
            $table->foreign('receiver_id')->references('id')->on('people');
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
        Schema::drop('samples');
    }
}
