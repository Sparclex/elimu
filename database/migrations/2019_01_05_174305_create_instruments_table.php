<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstrumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instruments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('instrument_id')->unique();
            $table->string('name')->index();
            $table->string('serial_number')->index();
            $table->unsignedInteger('responsible_id');

            $table->unsignedInteger('institution_id');
            $table->unsignedInteger('laboratory_id');
            $table->timestamps();

            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->foreign('laboratory_id')->references('id')->on('laboratories');
            $table->foreign('responsible_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instruments');
    }
}
