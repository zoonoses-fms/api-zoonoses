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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->decimal('statistic_coordinator_cost', 12, 2)->default(0)->nullable();
            $table->decimal('cold_chain_coordinator_cost', 12, 2)->default(0)->nullable();
            $table->decimal('cold_chain_nurse_cost', 12, 2)->default(0)->nullable();
            $table->decimal('statistic_cost', 12, 2)->default(0)->nullable();
            $table->decimal('cold_chain_cost', 12, 2)->default(0)->nullable();
            $table->decimal('zoonoses_cost', 12, 2)->default(0)->nullable();
            $table->decimal('transport_cost', 12, 2)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('statistic_coordinator_cost');
            $table->dropColumn('cold_chain_coordinator_cost');
            $table->dropColumn('statistic_cost');
            $table->dropColumn('cold_chain_cost');
            $table->dropColumn('zoonoses_cost');
            $table->dropColumn('transport_cost');
        });
    }
};
