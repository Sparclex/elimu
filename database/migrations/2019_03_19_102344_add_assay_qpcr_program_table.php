<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssayQpcrProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assay_qpcr_program', function(Blueprint $table) {
            $table->unsignedInteger('assay_id');
            $table->unsignedInteger('qpcr_program_id');

            $table->primary(['assay_id', 'qpcr_program_id']);

            $table->foreign('assay_id')->references('id')->on('assays')->onDelete('CASCADE');
            $table->foreign('qpcr_program_id')->references('id')->on('qpcr_programs')->onDelete('CASCADE');
        });

        Schema::table('assays', function(Blueprint $table) {
            $table->dropForeign('assays_qpcr_program_id_foreign');
            $table->dropColumn('qpcr_program_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assay_qpcr_program');
    }
}
