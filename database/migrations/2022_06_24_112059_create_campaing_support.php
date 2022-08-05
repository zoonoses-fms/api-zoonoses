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
        Schema::create('campaing_supports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_cycle_id')->nullable();
            $table->unsignedBigInteger('vaccination_support_id')->nullable();
            $table->unsignedBigInteger('coordinator_id')->nullable();
            $table->integer('goal')->default(0)->nullable();
            $table->timestamps();
            $table->foreign('campaign_cycle_id')->references('id')->on('campaign_cycles');
            $table->foreign('vaccination_support_id')->references('id')->on('vaccination_supports');
            $table->foreign('coordinator_id')
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
        Schema::dropIfExists('campaing_supports');
    }
};
