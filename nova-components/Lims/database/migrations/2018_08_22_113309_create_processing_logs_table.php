<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processing_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sample_id');
            $table->foreign('sample_id')->references('id')->on('samples');
            $table->unsignedBigInteger('test_id');
            $table->foreign('test_id')->references('id')->on('tests');
            $table->timestamp('processed_at')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('deliverer_id');
            $table->foreign('deliverer_id')->references('id')->on('people');
            $table->unsignedBigInteger('receiver_id');
            $table->foreign('receiver_id')->references('id')->on('people');
            $table->unsignedBigInteger('collector_id');
            $table->foreign('collector_id')->references('id')->on('people');
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
        Schema::drop('processing_logs');
    }
}
