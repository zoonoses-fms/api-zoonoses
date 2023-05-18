<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class TheBlock extends Model
{
    use HasFactory;

    public function neighborhood()
    {
        return $this->belongsTo(TheNeighborhood::class);
    }

    public function geography()
    {
        return $this->hasOne(TheBlockGeography::class);
    }

    public static function getGeoJSON(Request $request)
    {
        return TheBlock::select(
            'the_blocks.id',
            'the_blocks.gid as name',
            'the_blocks.description as description',
            'the_blocks.properties as properties',
            'the_blocks.gid'
        )
        ->selectRaw(
            'ST_AsGeoJSON(the_block_geographies.area) AS geojson'
        )
        ->join(
            'the_block_geographies',
            'the_block_geographies.the_block_id',
            '=',
            'the_blocks.id'
        )
        ->when($request->has('id'), function ($query) use ($request) {
            $id = $request->get('id');
            $query->where('the_blocks.the_neighborhood_id', $id);
        })
        ->get();
    }
}
