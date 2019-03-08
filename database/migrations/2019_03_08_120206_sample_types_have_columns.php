<?php

use App\Models\SampleType;
use App\Models\Study;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SampleTypesHaveColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sample_types', function(Blueprint $table) {
            $table->integer('columns')->nullable();
            $table->integer('rows')->nullable();
        });


        foreach(DB::table('storage_box_sizes')->get() as $storageSize) {
            $sampleType = SampleType::withoutGlobalScopes()->where('id', $storageSize->sample_type_id)->first();
            if(!$sampleType) {
                continue;
            }
            $sampleType->columns = $storageSize->columns;
            $sampleType->rows = $storageSize->rows;
            $sampleType->save();
        }

        Schema::dropIfExists('storage_box_sizes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sample_types', function(Blueprint $table) {
            $table->dropColumn('columns');
            $table->dropColumn('rows');
        });
    }
}
