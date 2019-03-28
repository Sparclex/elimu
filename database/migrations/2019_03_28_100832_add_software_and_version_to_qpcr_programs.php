<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftwareAndVersionToQpcrPrograms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qpcr_programs', function (Blueprint $table) {
            $table->string('software')->nullable();
            $table->string('version')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qpcr_programs', function (Blueprint $table) {
            $table->dropColumn('software');
            $table->dropColumn('version');
        });
    }
}
