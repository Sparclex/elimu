<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippedSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipped_samples', function (Blueprint $table) {
            $table->unsignedInteger('sample_id');
            $table->unsignedInteger('shipment_id');
            $table->integer('quantity')->default(1);

            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('CASCADE');
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('CASCADE');

            $table->primary(['sample_id', 'shipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipped_samples');
    }
}
