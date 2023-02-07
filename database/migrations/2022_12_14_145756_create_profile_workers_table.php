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
        Schema::create('profile_workers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('scope', ['campaign', 'cycle', 'support', 'point']);
            $table->enum('management', ['GEZOON', 'GETRANS', 'Rede de Frio']);
            $table->boolean('is_single_allocation')->default(true)->nullable();
            $table->integer('is_pre_campaign')->default(0)->nullable();
            $table->boolean('is_multiple')->default(false)->nullable();
            $table->boolean('is_rural')->default(false)->nullable();
            $table->boolean('is_pre_load')->default(false)->nullable();
            $table->boolean('is_supervisor')->default(false)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('profile_workers');
    }
};
