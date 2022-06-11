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
        Schema::create('collaring_dogs', function (Blueprint $table) {
            $table->id();
            $table->string('external_sample_number')->nullable();
            $table->string('laboratory_sample_number')->nullable();
            $table->integer('cycle');
            $table->string('dog_name')->nullable();
            $table->integer('age');
            $table->string('breed')->nullable();
            $table->enum('fur', ['short', 'long'])->nullable();
            $table->enum(
                'predominant_coat',
                ['black', 'white', 'caramel', 'brown', 'gray', 'brindle', 'other']
            )->nullable();
            $table->enum('sex', ['m', 'f'])->nullable();
            $table->enum('size', ['small', 'medium', 'large'])->nullable();
            $table->boolean('dog_collected')->default(false);
            $table->enum('dpp_result', ['positive', 'negative', 'inconclusive', 'not_performed'])->default(false);
            $table->enum('elisa_result', ['positive', 'negative', 'inconclusive', 'not_performed'])->default(false);
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
        Schema::dropIfExists('collaring_dogs');
    }
};
