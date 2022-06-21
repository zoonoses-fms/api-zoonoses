<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\MetaPhone;

class TheSubLocation extends MetaPhone
{
    use HasFactory;

    public function geography()
    {
        return $this->hasOne(TheSubLocationGeography::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(TheNeighborhood::class);
    }

    public function neighborhoodSpellingVariation()
    {
        return $this->hasMany(TheSubLocationSpellingVariation::class);
    }

    public static function getGeoJSON(Request $request)
    {
        return TheSubLocation::select(
            'the_sub_locations.id',
            'the_sub_locations.name',
            'the_sub_locations.gid'
        )
        ->selectRaw(
            'ST_AsGeoJSON(the_sub_location_geographies.area) AS geojson'
        )
        ->join('the_sub_location_geographies', 'the_sub_location_geographies.the_sub_location_id', '=', 'the_sub_locations.id')
        ->when($request->has('id'), function ($query) use ($request) {
            $id = $request->get('id');
            $query->where('the_sub_locations.the_neighborhood_id', $id);
        })
        ->get();
    }
}
