<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MetaPhone;

class TheNeighborhood extends MetaPhone
{
    use HasFactory;

    public function saad()
    {
        return $this->belongsTo(TheSaad::class);
    }

    public function blocks()
    {
        return $this->hasMany(TheBlock::class);
    }

    public function geography()
    {
        return $this->hasOne(TheNeighborhoodGeography::class);
    }

    public function populations()
    {
        return $this->hasMany(TheNeighborhoodPopulation::class);
    }

    public function neighborhoodSpellingVariation()
    {
        return $this->hasMany(TheNeighborhoodSpellingVariation::class);
    }

    public function populationByYear($year)
    {
        try {
            $population = $this->populations()
            ->where('year', $year - 1)
            ->first()->population;
            if ($population == null) {
                $population = $this->populations()
                ->where('year', $year - 1)
                ->first()->population;
            }

            return $population;
        } catch (\Throwable $th) {
            return null;
        }
    }
}
