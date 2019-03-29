<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveStorageConditionToSampleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sample_mutations', function(Blueprint $table) {
            $table->dropColumn('storage_conditions');
        });

        Schema::table('sample_types', function (Blueprint $table) {
            $table->string('storage_condition')->nullable();
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
            $table->dropColumn('storage_condition');
        });
    }
}
