<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeignKeysForStorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('storage', function (Blueprint $table) {
            $table->dropForeign('storage_sample_id_foreign');
            $table->dropForeign('storage_sample_type_id_foreign');
        });

        Schema::table('storage', function (Blueprint $table) {
            $table->foreign(['sample_id', 'sample_type_id'])
                ->references(['sample_id', 'sample_type_id'])
                ->on('sample_mutations')->onDelete('CASCADE');
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
