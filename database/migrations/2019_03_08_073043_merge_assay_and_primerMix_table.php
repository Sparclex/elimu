<?php

use App\Models\Assay;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MergeAssayAndPrimerMixTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assays', function(Blueprint $table) {
            $table->dropForeign('assays_primer_mix_id_foreign');
            $table->dropColumn('primer_mix_id');

            $table->unsignedInteger('reagent_id')->nullable();
            $table->float('reaction_volume')->nullable();
            $table->unsignedInteger('creator_id')->nullable();

            $table->foreign('reagent_id')->references('id')->on('reagents')->onDelete('SET NULL');
            $table->foreign('creator_id')->references('id')->on('people')->onDelete('CASCADE');
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
