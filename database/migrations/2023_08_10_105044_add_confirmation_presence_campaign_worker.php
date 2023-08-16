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
        Schema::table('campaign_worker', function (Blueprint $table) {
            $table->boolean('is_confirmation')->default(false);
            $table->boolean('is_presence')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_worker', function ($table) {
            $table->dropColumn('is_confirmation');
            $table->dropColumn('is_presence');
        });
    }
};
