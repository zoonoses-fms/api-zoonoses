<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_cold_chain_cycle', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_cold_chain_id');
            $table->unsignedBigInteger('campaign_cycle_id');
            $table->date('date')->nullable();
            $table->foreign('driver_cold_chain_id')->references('id')->on('vaccination_workers');
            $table->foreign('campaign_cycle_id')->references('id')->on('campaign_cycles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_cold_chain_cycle');
    }
};
