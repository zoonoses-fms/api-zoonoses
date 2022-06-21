<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
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

    public function subLocations()
    {
        return $this->hasMany(TheSubLocation::class);
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

    public static function getGeoJSON(Request $request)
    {
        return TheNeighborhood::select(
            'the_neighborhoods.id',
            'the_neighborhoods.name',
            'the_neighborhoods.gid'
        )
        ->selectRaw(
            'ST_AsGeoJSON(the_neighborhood_geographies.area) AS geojson'
        )
        ->join('the_neighborhood_geographies', 'the_neighborhood_geographies.the_neighborhood_id', '=', 'the_neighborhoods.id')
        ->when($request->has('id'), function ($query) use ($request) {
            $id = $request->get('id');
            $query->where('the_neighborhoods.id', $id);
        })
        ->get();
    }
}
