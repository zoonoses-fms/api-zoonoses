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

        Schema::rename('campaing_supports', 'campaign_supports');
        Schema::rename('campaing_points', 'campaign_points');

        Schema::table('campaign_points', function (Blueprint $table) {
            $table->renameColumn('campaing_support_id', 'campaign_support_id');
        });

        Schema::table('supervisor_support', function (Blueprint $table) {
            $table->renameColumn('campaing_support_id', 'campaign_support_id');
        });

        Schema::table('driver_support', function (Blueprint $table) {
            $table->renameColumn('campaing_support_id', 'campaign_support_id');
        });

        Schema::table('vaccinator_point', function (Blueprint $table) {
            $table->renameColumn('campaing_point_id', 'campaign_point_id');
        });

        Schema::table('assistant_support', function (Blueprint $table) {
            $table->renameColumn('campaing_support_id', 'campaign_support_id');
        });

        Schema::table('saad_support', function (Blueprint $table) {
            $table->renameColumn('campaing_support_id', 'campaign_support_id');
        });

        Schema::table('saad_point', function (Blueprint $table) {
            $table->renameColumn('campaing_point_id', 'campaign_point_id');
        });

        Schema::table('annotator_point', function (Blueprint $table) {
            $table->renameColumn('campaing_point_id', 'campaign_point_id');
        });

        Schema::table('vaccinator_support', function (Blueprint $table) {
            $table->renameColumn('campaing_support_id', 'campaign_support_id');
        });

        Schema::table('rural_supervisor_support', function (Blueprint $table) {
            $table->renameColumn('campaing_support_id', 'campaign_support_id');
        });

        Schema::table('rural_assistant_support', function (Blueprint $table) {
            $table->renameColumn('campaing_support_id', 'campaign_support_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
