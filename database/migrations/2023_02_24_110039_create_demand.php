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
        Schema::create('demand', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('establishment', ['residential', 'commercial', 'public']);
            $table->string('responsible_name')->nullable();
            $table->string('responsible_phone')->nullable();
            $table->string('responsible_cpf')->nullable();
            $table->enum('origin_of_demand', ['Ministério Público', 'Fiscalização', 'Refiscalização', 'Denúncia', 'Blitz']);
            $table->string('address')->nullable();
            $table->integer('address_number')->nullable();
            $table->bigInteger('demand_type_id')->unsigned();
            $table->timestamps();




            $table->foreign('demand_type_id')->references('id')->on('demand_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demand');
    }
};
