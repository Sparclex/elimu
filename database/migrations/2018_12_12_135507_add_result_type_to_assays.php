<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResultTypeToAssays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assays', function (Blueprint $table) {
            $table->string('result_type')->default('qPcr Rdml');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assays', function (Blueprint $table) {
            $table->dropColumn('result_type');
        });
    }
}
