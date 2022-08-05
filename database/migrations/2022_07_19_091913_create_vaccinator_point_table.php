<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaccinator_point', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vaccinator_id');
            $table->unsignedBigInteger('campaing_point_id');
            $table->foreign('vaccinator_id')->references('id')->on('vaccination_workers');
            $table->foreign('campaing_point_id')->references('id')->on('campaing_points');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaccinator_point');
    }
};
