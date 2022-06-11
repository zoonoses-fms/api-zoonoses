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
        Schema::create('collaring_dog_images', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('path');
            $table->unsignedBigInteger('collaring_dog_id');
            $table->timestamps();
            $table->foreign('collaring_dog_id')->references('id')->on('collaring_dogs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collaring_dog_images');
    }
};
