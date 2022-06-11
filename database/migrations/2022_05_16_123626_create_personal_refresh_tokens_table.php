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
        Schema::create('personal_refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->unsignedBigInteger('token_id');
            $table->string('name');
            $table->string('token', 128)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->foreign('token_id')->references('id')->on('personal_access_tokens')->onDelete('cascade');
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
        Schema::dropIfExists('personal_refresh_tokens');
    }
};
