<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssayControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assay_control', function (Blueprint $table) {
            $table->unsignedInteger('assay_id');
            $table->unsignedInteger('control_id');

            $table->primary(['assay_id', 'control_id']);

            $table->foreign('assay_id')->references('id')->on('assays');
            $table->foreign('control_id')->references('id')->on('controls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assay_control');
    }
}
