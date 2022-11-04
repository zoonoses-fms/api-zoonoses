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
        Schema::table('campaign_cycles', function (Blueprint $table) {
            $table->unsignedBigInteger('statistic_coordinator_id')->nullable();
            $table->unsignedBigInteger('cold_chain_coordinator_id')->nullable();
            $table->unsignedBigInteger('cold_chain_nurse_id')->nullable();
            $table->foreign('statistic_coordinator_id')
              ->references('id')->on('vaccination_workers');
            $table->foreign('cold_chain_coordinator_id')
              ->references('id')->on('vaccination_workers');
            $table->foreign('cold_chain_nurse_id')
            ->references('id')->on('vaccination_workers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_cycles', function (Blueprint $table) {
            $table->dropColumn('statistic_coordinator_id');
            $table->dropColumn('cold_chain_coordinator_id');
            $table->dropColumn('cold_chain_nurse_id');
        });
    }
};
