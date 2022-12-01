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
        Schema::table('campaign_cycles', function (Blueprint $table) {
            $table->boolean('partial_value')->default(false);
            $table->integer('percentage_value')->default(0);
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
            $table->dropColumn('partial_value');
            $table->dropColumn('percentage_value');
        });
    }
};
