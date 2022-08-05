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
        Schema::create('assistant_support', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assistant_id');
            $table->unsignedBigInteger('campaing_support_id');
            $table->foreign('assistant_id')->references('id')->on('vaccination_workers');
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
        Schema::dropIfExists('assistant_support');
    }
};
