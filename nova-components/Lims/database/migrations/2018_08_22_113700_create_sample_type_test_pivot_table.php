<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleTypeTestPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_type_test', function (Blueprint $table) {
            $table->bigInteger('sample_type_id')->unsigned()->index();
            $table->foreign('sample_type_id')->references('id')->on('sample_types')->onDelete('cascade');
            $table->bigInteger('test_id')->unsigned()->index();
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            $table->primary(['sample_type_id', 'test_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sample_type_test');
    }
}
