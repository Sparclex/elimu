<?php

use App\Models\Assay;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveReagentIdFromAssaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $assays = Assay::withoutGlobalScopes()->get();
        $assays->each(
            function ($assay) {
                if (! $assay->reagent_id) {
                    return true;
                }
                DB::table('assay_reagent')->insert(
                    [
                        'assay_id' => $assay->id,
                        'reagent_id' => $assay->reagent_id,
                    ]);
            }
        );
        Schema::table(
            'assays', function (Blueprint $table) {
            $table->dropForeign(['reagent_id']);
            $table->dropColumn('reagent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'assays', function (Blueprint $table) {
            //
        });
    }
}
