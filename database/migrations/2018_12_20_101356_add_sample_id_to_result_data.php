<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSampleIdToResultData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('result_data', function (Blueprint $table) {
            $table->string('sample_id')->nullable();
        });

        DB::table('result_data')->update(['sample_id' => DB::raw('extra->>"$.\"sample ID\""')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('result_data', function (Blueprint $table) {
            $table->dropColumn('sample_id');
        });
    }
}
