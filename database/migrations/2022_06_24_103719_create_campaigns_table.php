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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->date('start');
            $table->date('end')->nullable();
            $table->integer('goal')->nullable();
            $table->unsignedBigInteger('coordinator_id')->nullable();
            $table->decimal('coordinator_cost', 12, 2)->default(0)->nullable();
            $table->decimal('supervisor_cost', 12, 2)->default(0)->nullable();
            $table->decimal('assistant_cost', 12, 2)->default(0)->nullable();
            $table->decimal('vaccinator_cost', 12, 2)->default(0)->nullable();
            $table->decimal('vaccine_cost', 12, 2)->default(0)->nullable();
            $table->decimal('mileage_cost', 12, 2)->default(0)->nullable();
            $table->decimal('driver_cost', 12, 2)->default(0)->nullable();
            $table->foreign('coordinator_id')
              ->references('id')->on('vaccination_workers');
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
        Schema::dropIfExists('campaigns');
    }
};
