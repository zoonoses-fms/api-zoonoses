<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_points', function (Blueprint $table) {
            $table->integer('goal_cats')->default(0)->nullable();
            $table->integer('goal_dogs')->default(0)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_points', function ($table) {
            $table->dropColumn('goal_cats');
            $table->dropColumn('goal_dogs');
        });
    }
};
