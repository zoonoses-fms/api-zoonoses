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
        $this->createSaads();
        $this->createNeighborhoods();
        $this->createBlocks();
        $this->createSaadGeographies();
        $this->createNeighborhoodGeographies();
        $this->createBlockGeographies();
        $this->createNeighborhoodSpellingVariations();
        $this->createNeighborhoodPopulations();
        $this->createLocations();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropLocations();
        $this->dropNeighborhoodPopulations();
        $this->dropNeighborhoodSpellingVariations();
        $this->dropBlockGeographies();
        $this->dropNeighborhoodGeographies();
        $this->dropSaadGeographies();
        $this->dropBlocks();
        $this->dropNeighborhoods();
        $this->dropSaads();
    }

    /**
     * Run the create the_saads.
     *
     * @return void
     */
    public function createSaads()
    {
        Schema::create('the_saads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('standardized');
            $table->string('metaphone');
            $table->string('soundex');
            $table->integer('gid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the the_saad.
     *
     * @return void
     */
    public function dropSaads()
    {
        Schema::dropIfExists('the_saads');
    }

    /**
     * Run the create the_neighborhoods.
     *
     * @return void
     */
    public function createNeighborhoods()
    {
        Schema::create('the_neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('standardized');
            $table->string('metaphone');
            $table->string('soundex');
            $table->integer('gid');
            $table->unsignedBigInteger('the_saad_id')->nullable();
            $table->timestamps();
            $table->foreign('the_saad_id')->references('id')->on('the_saads');
        });
    }

    /**
     * Reverse the the_neighborhoods.
     *
     * @return void
     */
    public function dropNeighborhoods()
    {
        Schema::dropIfExists('the_neighborhoods');
    }
    // block

    /**
     * Run the create the_blocks.
     *
     * @return void
     */
    public function createBlocks()
    {
        Schema::create('the_blocks', function (Blueprint $table) {
            $table->id();
            $table->integer('gid');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('the_neighborhood_id')->nullable();
            $table->timestamps();
            $table->foreign('the_neighborhood_id')->references('id')->on('the_neighborhoods');
        });
    }

    /**
     * Reverse the the_blocks.
     *
     * @return void
     */
    public function dropBlocks()
    {
        Schema::dropIfExists('the_blocks');
    }

    /**
     * Run the create the_locations.
     *
     * @return void
     */
    public function createLocations()
    {
        Schema::create('the_locations', function (Blueprint $table) {
            $table->id();
            $table->integer('gid')->nullable();
            $table->string('name');
            $table->string('standardized');
            $table->string('metaphone');
            $table->string('soundex');
            $table->unsignedBigInteger('the_neighborhood_id')->nullable();
            $table->timestamps();
            $table->foreign('the_neighborhood_id')->references('id')->on('the_neighborhoods');
        });
    }

    /**
     * Reverse the the_locations.
     *
     * @return void
     */
    public function dropLocations()
    {
        Schema::dropIfExists('the_locations');
    }

    /**
     * Run the create the_neighborhood_zone_geographies.
     *
     * @return void
     */
    public function createSaadGeographies()
    {
        Schema::create('the_saad_geographies', function (Blueprint $table) {
            $table->id();
            $table->geometry('area', 'GEOMETRY', '3857');
            $table->unsignedBigInteger('the_saad_id');
            $table->timestamps();
            $table->foreign('the_saad_id')->references('id')->on('the_saads');
        });
    }

    /**
     * Reverse the the_neighborhood_zone_geographies.
     *
     * @return void
     */
    public function dropSaadGeographies()
    {
        Schema::dropIfExists('the_saad_geographies');
    }

    /**
    * Run the create the_neighborhood_geographies.
    *
    * @return void
    */
    public function createNeighborhoodGeographies()
    {
        Schema::create('the_neighborhood_geographies', function (Blueprint $table) {
            $table->id();
            $table->geometry('area', 'GEOMETRY', '3857');
            $table->unsignedBigInteger('the_neighborhood_id');
            $table->timestamps();
            $table->foreign('the_neighborhood_id')->references('id')->on('the_neighborhoods');
        });
    }

    /**
     * Reverse the the_neighborhood_geographies.
     *
     * @return void
     */
    public function dropNeighborhoodGeographies()
    {
        Schema::dropIfExists('the_neighborhood_geographies');
    }

    /**
     * Run the create the_block_geographies.
     *
     * @return void
     */
    public function createBlockGeographies()
    {
        Schema::create('the_block_geographies', function (Blueprint $table) {
            $table->id();
            $table->geometry('area', 'GEOMETRY', '3857');
            $table->unsignedBigInteger('the_block_id');
            $table->timestamps();
            $table->foreign('the_block_id')->references('id')->on('the_blocks');
        });
    }

    /**
     * Reverse the the_block_geographies.
     *
     * @return void
     */
    public function dropBlockGeographies()
    {
        Schema::dropIfExists('the_block_geographies');
    }

    /**
     * Run the create the_block_geographies.
     *
     * @return void
     */
    public function createLocationGeographies()
    {
        Schema::create('the_location_geographies', function (Blueprint $table) {
            $table->id();
            $table->geometry('area', 'GEOMETRY', '3857');
            $table->unsignedBigInteger('the_location_id');
            $table->timestamps();
            $table->foreign('the_location_id')->references('id')->on('the_locations');
        });
    }

    /**
     * Reverse the the_block_geographies.
     *
     * @return void
     */
    public function dropLocationGeographies()
    {
        Schema::dropIfExists('the_location_geographies');
    }

    /**
     * Run the create the_neighborhood_spelling_variations.
     *
     * @return void
     */
    public function createNeighborhoodSpellingVariations()
    {
        Schema::create('the_neighborhood_spelling_variations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('standardized');
            $table->string('metaphone');
            $table->string('soundex');
            $table->unsignedInteger('the_neighborhood_id')->nullable();
            $table->timestamps();
            $table->foreign('the_neighborhood_id')->references('id')->on('the_neighborhoods');
        });
    }

    /**
     * Reverse the the_neighborhood_spelling_variations.
     *
     * @return void
     */
    public function dropNeighborhoodSpellingVariations()
    {
        Schema::dropIfExists('the_neighborhood_spelling_variations');
    }

    /**
     * Run the create the_neighborhood_populations.
     *
     * @return void
     */
    public function createNeighborhoodPopulations()
    {
        Schema::create('the_neighborhood_populations', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->bigInteger('population');
            $table->unsignedBigInteger('the_neighborhood_id');
            $table->timestamps();
            $table->foreign('the_neighborhood_id')->references('id')->on('the_neighborhoods');
        });
    }

    /**
     * Reverse the the_neighborhood_populations.
     *
     * @return void
     */
    public function dropNeighborhoodPopulations()
    {
        Schema::dropIfExists('the_neighborhood_populations');
    }
};
