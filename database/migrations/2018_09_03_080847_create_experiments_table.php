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
            $table->bigInteger('reagent_id')->unsigned();
            $table->timestamp('requested_at')->nullable();
            $table->bigInteger('requester_id')->unsigned()->nullable();
            $table->text('comment')->nullable();
            $table->string('result_file')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('result_type')->nullable();
            $table->timestamps();

            $table->foreign('reagent_id')->references('id')->on('reagents')->onDelete('CASCADE');
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
