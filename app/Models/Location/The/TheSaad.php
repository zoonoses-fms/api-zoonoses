<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\MetaPhone;

class TheSaad extends MetaPhone
{
    use HasFactory;


    public function neighborhoods()
    {
        return $this->hasMany(TheNeighborhood::class);
    }

    public function geography()
    {
        return $this->hasOne(TheSaadGeography::class);
    }

    public static function getGeoJSON(Request $request)
    {
        return TheSaad::select(
            'the_saads.id',
            'the_saads.name',
            'the_saads.gid'
        )
        ->selectRaw(
            'ST_AsGeoJSON(the_saad_geographies.area) AS geojson'
        )
        ->join(
            'the_saad_geographies',
            'the_saad_geographies.the_saad_id',
            '=',
            'the_saads.id'
        )
        ->when($request->has('id'), function ($query) use ($request) {
            $id = $request->get('id');
            $query->where('the_saads.id', $id);
        })
        ->get();
    }
}
