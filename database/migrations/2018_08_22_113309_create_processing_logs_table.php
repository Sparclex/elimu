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
            $table->timestamp('processed_at')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('deliverer_id');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('collector_id');
            $table->timestamps();

            $table->foreign('deliverer_id')->references('id')->on('people');
            $table->foreign('receiver_id')->references('id')->on('people');
            $table->foreign('collector_id')->references('id')->on('people');
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
