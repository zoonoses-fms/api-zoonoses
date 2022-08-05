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
        Schema::create('saad_support', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('saad_id');
            $table->unsignedBigInteger('campaing_support_id');
            $table->foreign('saad_id')->references('id')->on('the_saads');
            $table->foreign('campaing_support_id')->references('id')->on('campaing_supports');
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
        Schema::dropIfExists('saad_support');
    }
};
