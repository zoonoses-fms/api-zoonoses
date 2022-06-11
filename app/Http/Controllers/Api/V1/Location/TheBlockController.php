<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location\The\TheBlock;
use App\Models\Location\The\TheNeighborhood;

class TheBlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('type')) {
            $type = $request->get('type');
            if ($type == 'list') {
                $theBlocks = TheNeighborhood::orderBy('the_neighborhoods.name')->get();
            } elseif ($type == 'geojson') {
                $theBlocks = TheBlock::select(
                    'the_blocks.id',
                    'the_blocks.gid as name',
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
        } else {
            if ($request->has('per_page')) {
                $perPage = $request->input('per_page');
            } else {
                $perPage = 10;
            }

            $theBlocks = TheBlock::paginate($perPage);
        }

        return $theBlocks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
