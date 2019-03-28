<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowEmptyInstrumentAndProtocolForAssaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assays', function(Blueprint $table) {
            $table->unsignedInteger('instrument_id')->nullable()->change();
            $table->unsignedInteger('protocol_id')->nullable()->change();

            $table->dropForeign(['instrument_id']);
            $table->dropForeign( ['protocol_id']);

            $table->foreign('instrument_id')->references('id')->on('instruments')->onDelete('SET NULL');
            $table->foreign('protocol_id')->references('id')->on('protocols')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
