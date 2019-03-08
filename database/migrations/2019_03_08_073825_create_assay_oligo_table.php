<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssayOligoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assay_oligo', function (Blueprint $table) {
            $table->unsignedInteger('oligo_id');
            $table->unsignedInteger('assay_id');
            $table->float('concentration')->default(100);

            $table->foreign('oligo_id')->references('id')->on('oligos');
            $table->foreign('assay_id')->references('id')->on('assays');

            $table->primary(['oligo_id', 'assay_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assay_oligo');
    }
}
