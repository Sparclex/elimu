<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudyIdToSampleTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sample_types', function (Blueprint $table) {
            $table->unsignedInteger('study_id');

            $table->foreign('study_id')->references('id')->on('studies')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sample_types', function (Blueprint $table) {
            $table->dropColumn('study_id');
        });
    }
}
