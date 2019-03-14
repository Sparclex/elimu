<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQpcrProgramToAssaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assays', function (Blueprint $table) {
            $table->unsignedInteger('qpcr_program_id')->nullable();

            $table->foreign('qpcr_program_id')
                ->references('id')
                ->on('qpcr_programs')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
