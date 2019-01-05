<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAssaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('assays')->delete();

        Schema::table('assays', function (Blueprint $table) {
            $table->string('definition_file')->nullable();
            $table->json('parameters');

            $table->bigInteger('instrument_id')->references('id')->on('instruments');
            $table->bigInteger('protocol_id')->references('id')->on('protocols');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
