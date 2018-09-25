<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReagentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reagents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lot')->unique();
            $table->string('name');
            $table->date('expires_at')->nullable();
            $table->bigInteger('assay_id')->unsigned();
            $table->timestamps();

            $table->foreign('assay_id')->references('id')->on('assays');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reagents');
    }
}
