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
        Schema::create('campaing_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaing_support_id')->nullable();
            $table->unsignedBigInteger('vaccination_point_id')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->integer('goal')->default(0)->nullable();
            $table->integer('male_dog_under_4m')->default(0)->nullable();
            $table->integer('female_dog_under_4m')->default(0)->nullable();
            $table->integer('male_dog_major_4m_under_1y')->default(0)->nullable();
            $table->integer('female_dog_major_4m_under_1y')->default(0)->nullable();
            $table->integer('male_dog_major_1y_under_2y')->default(0)->nullable();
            $table->integer('female_dog_major_1y_under_2y')->default(0)->nullable();
            $table->integer('male_dog_major_2y_under_4y')->default(0)->nullable();
            $table->integer('female_dog_major_2y_under_4y')->default(0)->nullable();
            $table->integer('male_dog_major_4y')->default(0)->nullable();
            $table->integer('female_dog_major_4y')->default(0)->nullable();
            $table->integer('male_dogs')->default(0)->nullable();
            $table->integer('female_dogs')->default(0)->nullable();
            $table->integer('total_of_dogs')->default(0)->nullable();
            $table->integer('male_cat')->default(0)->nullable();
            $table->integer('female_cat')->default(0)->nullable();
            $table->integer('total_of_cats')->default(0)->nullable();
            $table->integer('total')->default(0)->nullable();
            $table->integer('bottle_received')->default(0)->nullable();
            $table->integer('bottle_used_completely')->default(0)->nullable();
            $table->integer('bottle_used_partially')->default(0)->nullable();
            $table->integer('bottle_returned_completely')->default(0)->nullable();
            $table->integer('bottle_returned_partially')->default(0)->nullable();
            $table->integer('bottle_lost')->default(0)->nullable();
            $table->timestamps();
            $table->foreign('campaing_support_id')->references('id')->on('campaing_supports');
            $table->foreign('vaccination_point_id')->references('id')->on('vaccination_points');
            $table->foreign('supervisor_id')->references('id')->on('vaccination_workers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaing_points');
    }
};
