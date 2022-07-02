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
        Schema::create('vaccination_campaing_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vaccination_campaing_support_id')->nullable();
            $table->unsignedBigInteger('vaccination_point_id')->nullable();
            $table->integer('goal')->nullable();
            $table->timestamps();
            $table->foreign('vaccination_campaing_support_id')->references('id')->on('vaccination_campaing_supports');
            $table->foreign('vaccination_point_id')->references('id')->on('vaccination_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaccination_campaing_points');
    }
};
