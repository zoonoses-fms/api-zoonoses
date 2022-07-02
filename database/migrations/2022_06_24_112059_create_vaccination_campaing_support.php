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
        Schema::create('vaccination_campaing_supports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vaccination_campaign_id')->nullable();
            $table->unsignedBigInteger('vaccination_support_id')->nullable();
            $table->unsignedBigInteger('vaccination_supervisor_id')->nullable();
            $table->integer('goal')->nullable();
            $table->timestamps();
            $table->foreign('vaccination_campaign_id')->references('id')->on('vaccination_campaigns');
            $table->foreign('vaccination_support_id')->references('id')->on('vaccination_supports');
            $table->foreign('vaccination_supervisor_id')
              ->references('id')->on('vaccination_supervisors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaccination_campaing_supports');
    }
};
