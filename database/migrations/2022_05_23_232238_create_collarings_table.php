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
        Schema::create('collarings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('the_saad_id');
            $table->unsignedBigInteger('the_neighborhood_id');
            $table->unsignedBigInteger('the_block_id')->nullable();
            $table->unsignedBigInteger('the_location_id')->nullable();
            $table->integer('address_number')->nullable();
            $table->string('landmark')->nullable();
            $table->string('owner_name');
            $table->string('phone');
            $table->timestamps();
            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('the_saad_id')->references('id')->on('the_saads');
            $table->foreign('the_neighborhood_id')->references('id')->on('the_neighborhoods');
            $table->foreign('the_block_id')->references('id')->on('the_blocks');
            $table->foreign('the_location_id')->references('id')->on('the_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collarings');
    }
};
