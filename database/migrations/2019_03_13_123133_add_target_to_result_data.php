<?php

use App\Models\ResultData;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetToResultData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('result_data', function (Blueprint $table) {
            $table->string('target')->nullable();
        });

        ResultData::withoutGlobalScopes()->get()->each(function ($data) {
            $data->target = $data->extra['target'] ?? null;
            $data->save();
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
            $table->dropColumn('target');
        });
    }
}
