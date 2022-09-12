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
        Schema::table('vaccination_workers', function (Blueprint $table) {
            $table->enum('type', ['ace', 'acs', 'fms'])->default('fms')->nullable();
            $table->string('cpf')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vaccination_workers', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
