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
        Schema::create('vaccination_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('address_complement')->nullable();
            $table->unsignedBigInteger('the_neighborhood_spelling_variation_id')->nullable();
            $table->geometry('geometry', 'GEOMETRY', '3857')->nullable();
            $table->timestamps();
            $table->foreign('the_neighborhood_spelling_variation_id')
              ->references('id')->on('the_neighborhood_spelling_variations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaccination_points');
    }
};
