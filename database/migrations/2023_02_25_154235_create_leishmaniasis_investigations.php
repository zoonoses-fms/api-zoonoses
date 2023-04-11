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
        Schema::create('leishmaniasis_investigations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('responsible')->nullable();
            $table->date('birth');
            $table->string('phone')->nullable();
            $table->enum('sex', ['m', 'f'])->nullable();
            $table->string('address')->nullable();
            $table->integer('address_number')->nullable();
            $table->unsignedBigInteger('the_saad_id');
            $table->unsignedBigInteger('the_neighborhood_id');
            $table->unsignedBigInteger('the_block_id')->nullable();
            $table->unsignedBigInteger('the_sub_location_id')->nullable();
            $table->boolean('recently_moved')->nullable();
            $table->date('date_moved')->nullable();
            $table->string('old_address')->nullable();
            $table->boolean('another_patient')->default(false);
            $table->boolean('medical_assistance')->default(true);
            $table->date('date_medical_assistance')->nullable();
            $table->integer('diagnosis_care_number')->nullable();
            $table->boolean('hospitalized')->default(false);
            $table->date('date_hospitalized')->nullable();
            $table->boolean('hospitalized_diagnosis')->default(false);
            $table->string('hospital_name')->nullable();
            $table->string('doctor_name')->nullable();
            $table->boolean('has_prescription')->default(false);
            $table->string('medication_name')->nullable();
            $table->integer('total_doses_applied')->nullable();




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
        Schema::dropIfExists('leishmaniasis_investigations');
    }
};
