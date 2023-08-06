<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location\The\TheBlock;
use App\Models\Location\The\TheBlockGeography;
use App\Models\Location\The\TheNeighborhood;
use App\Models\Location\The\TheSaad;
use Illuminate\Support\Facades\DB;

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
                $theBlocks = TheSaad::orderBy('the_saads.name')->with('neighborhoods')->get();
            } elseif ($type == 'geojson') {
                $theBlocks = TheBlock::getGeoJSON($request);
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

        try {
            $feature = $request->all();

            $block = new TheBlock();
            $block->gid = trim($feature['properties']['name']);
            $block->description = isset($feature['properties']['description']) ? trim($feature['properties']['description']) : null;
            $block->properties = isset($feature['properties']['properties']) ? trim($feature['properties']['properties']) : null;
            $block->the_neighborhood_id = $feature['properties']['feature_id'];

            $blockGeography = new TheBlockGeography();
            $geometry = json_encode($feature['geometry']);
            $blockGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
            $block->save();
            $block->geography()->save($blockGeography);

            return $this->success('created', 201);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('internal error', 503, $th);
        }
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
        try {
            $feature = $request->all();

            $block = TheBlock::find($id);
            $block->gid = trim($feature['properties']['name']);
            if(isset($feature['properties']['description'])) {
                $block->description = trim($feature['properties']['description']);
            }

            if (isset($feature['properties']['properties'])) {
                $block->properties = trim($feature['properties']['properties']);
            }

            $block->the_neighborhood_id = $feature['properties']['feature_id'];
            $blockGeography = $block->geography;
            $geometry = json_encode($feature['geometry']);
            $blockGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
            $block->save();
            $blockGeography->save();

            return $this->success('created', 201);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('internal error', 503, $th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $block = TheBlock::find($id);
        $block->geography->delete();

        $block->delete();
    }
}
