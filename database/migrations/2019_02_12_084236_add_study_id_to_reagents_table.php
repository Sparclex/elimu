<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudyIdToReagentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reagents', function (Blueprint $table) {
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
        Schema::table('reagents', function (Blueprint $table) {
            $table->dropForeign('reagents_study_id_foreign');

            $table->dropColumn('study_id');
        });
    }
}
