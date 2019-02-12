<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProtocolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protocols', function (Blueprint $table) {
            $table->increments('id');
            $table->string('protocol_id')->unique();
            $table->string('name')->index();
            $table->string('version');
            $table->date('implemented_at');
            $table->string('attachment_name');
            $table->string('attachment_path');
            $table->unsignedInteger('responsible_id');
            $table->unsignedInteger('institution_id');
            $table->unsignedInteger('study_id');
            $table->timestamps();

            $table->foreign('study_id')->references('id')->on('studies');
            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->foreign('responsible_id')->references('id')->on('people');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('protocols');
    }
}
