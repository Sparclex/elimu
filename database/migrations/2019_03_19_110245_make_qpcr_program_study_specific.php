<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeQpcrProgramStudySpecific extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('qpcr_programs')->delete();

        Schema::table('qpcr_programs', function(Blueprint $table) {
            $table->unsignedInteger('study_id');

            $table->foreign('study_id')->references('id')->on('studies');
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
