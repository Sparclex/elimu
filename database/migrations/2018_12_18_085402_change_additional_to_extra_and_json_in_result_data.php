<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAdditionalToExtraAndJsonInResultData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('results')->delete();
        Schema::table('result_data', function (Blueprint $table) {
            $table->dropColumn('additional');
            $table->json('extra')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('result_data', function (Blueprint $table) {
            //
        });
    }
}
