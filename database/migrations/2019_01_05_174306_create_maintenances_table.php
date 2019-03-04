<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('instrument_id');
            $table->unsignedInteger('technician_id');
            $table->date('date');
            $table->text('procedure')->nullable();
            $table->timestamps();

            $table->foreign('instrument_id')->references('id')->on('instruments');
            $table->foreign('technician_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenances');
    }
}
