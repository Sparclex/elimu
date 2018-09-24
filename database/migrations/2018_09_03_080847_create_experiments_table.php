<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExperimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'experiments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('study_id')->unsigned();
            $table->bigInteger('assay_id')->unsigned();
            $table->timestamp('requested_at')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('requester_id')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('assay_id')->references('id')->on('assays')->onDelete('CASCADE');
            $table->foreign('study_id')->references('id')->on('studies')->onDelete('CASCADE');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experiments');
    }
}
