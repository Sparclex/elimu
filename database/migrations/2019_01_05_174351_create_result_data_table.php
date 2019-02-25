<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('result_id')->unsigned();
            $table->integer('experiment_id')->unsigned();
            $table->integer('study_id')->unsigned();
            $table->float('primary_value', 12, 8)->nullable();
            $table->string('secondary_value')->nullable();
            $table->json('extra')->nullable();
            $table->boolean('included')->default(true);
            $table->timestamps();

            $table->foreign('result_id')
                ->references('id')
                ->on('results')
                ->onDelete('CASCADE');
            $table->foreign('experiment_id')
                ->references('id')
                ->on('experiments')
                ->onDelete('CASCADE');
            $table->foreign('study_id')
                ->references('id')
                ->on('studies')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result_data');
    }
}
