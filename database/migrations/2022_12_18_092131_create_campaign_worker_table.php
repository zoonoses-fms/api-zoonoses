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
        Schema::create('campaign_worker', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('campaign_cycle_id')->nullable();
            $table->unsignedBigInteger('campaign_support_id')->nullable();
            $table->unsignedBigInteger('campaign_point_id')->nullable();
            $table->unsignedBigInteger('profile_workers_id');
            $table->unsignedBigInteger('vaccination_worker_id');
            $table->integer('is_pre_campaign')->default(0)->nullable();
            $table->boolean('is_single_allocation')->default(true)->nullable();
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('campaign_cycle_id')->references('id')->on('campaign_cycles');
            $table->foreign('campaign_support_id')->references('id')->on('campaign_supports');
            $table->foreign('campaign_point_id')->references('id')->on('campaign_points');
            $table->foreign('profile_workers_id')->references('id')->on('profile_workers');
            $table->foreign('vaccination_worker_id')->references('id')->on('vaccination_workers');
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
        Schema::dropIfExists('campaign_worker');
    }
};
