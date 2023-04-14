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
        Schema::create('rubber_shops', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name')->nullable();
            $table->string('owner')->nullable();
            $table->string('phone')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->nullable();
            $table->string('cpf_cnpj')->nullable();
            $table->string('address_complement')->nullable();
            $table->unsignedBigInteger('saad_id');
            $table->unsignedBigInteger('the_neighborhood_alias_id')->nullable();
            $table->geometry('geometry', 'GEOMETRY', '3857')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('the_neighborhood_alias_id')->references('id')->on('the_neighborhood_aliases');
            $table->foreign('saad_id')->references('id')->on('the_saads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rubber_shops');
    }
};
