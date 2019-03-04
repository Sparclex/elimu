<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();

            $table->unsignedInteger('assay_definition_file_id');
            $table->unsignedInteger('instrument_id');
            $table->unsignedInteger('protocol_id');
            $table->unsignedInteger('primer_mix_id');
            $table->unsignedInteger('study_id');
            $table->timestamps();

            $table->unique(['name', 'study_id']);

            $table->foreign('study_id')
                ->references('id')
                ->on('studies')
                ->onDelete('CASCADE');
            $table->foreign('primer_mix_id')
                ->references('id')
                ->on('primer_mixes')
                ->onDelete('CASCADE');
            $table->foreign('instrument_id')
                ->references('id')
                ->on('instruments')
                ->onDelete('CASCADE');
            $table->foreign('protocol_id')
                ->references('id')
                ->on('protocols')
                ->onDelete('CASCADE');
            $table->foreign('assay_definition_file_id')
                ->references('id')
                ->on('assay_definition_files')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assays');
    }
}
