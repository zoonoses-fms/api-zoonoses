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
        Schema::create('statistic_cycle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('statistic_id');
            $table->unsignedBigInteger('campaign_cycle_id');
            $table->foreign('statistic_id')->references('id')->on('vaccination_workers');
            $table->foreign('campaign_cycle_id')->references('id')->on('campaign_cycles');
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
        Schema::dropIfExists('statistic_cycle');
    }
};
