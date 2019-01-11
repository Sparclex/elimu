<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestedExperimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requested_experiments', function (Blueprint $table) {
            $table->integer('experiment_id')->unsigned();
            $table->integer('sample_id')->unsigned();

            $table->foreign('experiment_id')
                ->references('id')
                ->on('experiments')
                ->onDelete('CASCADE');
            $table->foreign('sample_id')
                ->references('id')
                ->on('samples')
                ->onDelete('CASCADE');

            $table->primary(['experiment_id', 'sample_id'], 'requested_experiments_primary');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requested_experiments');
    }
}
