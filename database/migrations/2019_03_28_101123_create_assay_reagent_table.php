<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssayReagentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assay_reagent', function (Blueprint $table) {
            $table->unsignedInteger('assay_id');
            $table->unsignedInteger('reagent_id');

            $table->primary(['assay_id', 'reagent_id']);

            $table->foreign('assay_id')->references('id')->on('assays')->onDelete('CASCADE');
            $table->foreign('reagent_id')->references('id')->on('reagents')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assay_reagent');
    }
}
