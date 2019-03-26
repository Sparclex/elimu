<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveReactionValueToQpcrPrograms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assays', function(Blueprint $table) {
            $table->dropColumn('reaction_volume');
        });
        Schema::table('qpcr_programs', function (Blueprint $table) {
            $table->text('reaction_volume')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assays', function(Blueprint $table) {
            $table->text('reaction_volume')->nullable();
        });
        Schema::table('qpcr_programs', function (Blueprint $table) {
            $table->dropColumn('reaction_volume');
        });
    }
}
