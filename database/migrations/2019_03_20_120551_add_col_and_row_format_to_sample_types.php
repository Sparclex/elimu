<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColAndRowFormatToSampleTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sample_types', function (Blueprint $table) {
            $table->string('column_format')->default('ABC');
            $table->string('row_format')->default('123');
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
            $table->dropColumn('column_format');
            $table->dropColumn('row_format');
        });
    }
}
