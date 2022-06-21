<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use App\Models\Location\The\TheBlock;
use Illuminate\Http\Request;
use App\Models\Location\The\TheNeighborhood;
use App\Models\Location\The\TheNeighborhoodGeography;
use App\Models\Location\The\TheSaad;
use Illuminate\Support\Facades\DB;

class TheNeighborhoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('keyword')) {

            $keyword = $request->get('keyword');
            $theNeighborhoods = TheNeighborhood::searchByName($keyword);
        } elseif ($request->has('type')) {

            $type = $request->get('type');
            if ($type == 'list') {
                $theNeighborhoods = TheSaad::orderBy('the_saads.name')->with('neighborhoods')->get();
            } elseif ($type == 'geojson') {
                $theNeighborhoods = TheNeighborhood::getGeoJSON($request);
            }
        } else {
            if ($request->has('per_page')) {
                $perPage = $request->input('per_page');
            } else {
                $perPage = 10;
            }

            $theNeighborhoods = TheNeighborhood::paginate($perPage);
        }


        return $theNeighborhoods;
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

            $lastNeighborhood  = TheNeighborhood::orderBy('gid', 'desc')->first();

            $neighborhood = new TheNeighborhood();
            $name = trim($feature['properties']['name']);
            $neighborhood->name = $name;
            $neighborhood->standardized = $neighborhood->nameCase($name);
            $neighborhood->metaphone = $neighborhood->getPhraseMetaphone($name);
            $neighborhood->soundex = soundex($name);
            $neighborhood->gid = $lastNeighborhood != null ? $lastNeighborhood->gid + 1 : 1;
            $neighborhood->the_saad_id = $feature['properties']['region_id'];
            $neighborhoodGeography = new TheNeighborhoodGeography();
            $geometry = json_encode($feature['geometry']);
            $neighborhoodGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
            $neighborhood->save();
            $neighborhood->geography()->save($neighborhoodGeography);

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

            $neighborhood = TheNeighborhood::find($id);
            $name = trim($feature['properties']['name']);
            $neighborhood->name = $name;
            $neighborhood->standardized = $neighborhood->nameCase($name);
            $neighborhood->metaphone = $neighborhood->getPhraseMetaphone($name);
            $neighborhood->soundex = soundex($name);
            $neighborhood->the_saad_id = $feature['properties']['region_id'];
            $neighborhoodGeography = $neighborhood->geography;
            $geometry = json_encode($feature['geometry']);
            $neighborhoodGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
            $neighborhood->save();
            $neighborhood->geography()->save($neighborhoodGeography);

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
        $neighborhood = TheNeighborhood::find($id);
        $blocks = $neighborhood->blocks;
        $subLocations = $neighborhood->subLocations;

        foreach ($blocks as $block) {
            $block->delete();
        }

        foreach ($subLocations as $subLocation) {
            $subLocation->delete();
        }

        $neighborhood->geography->delete();

        $neighborhood->delete();
    }
}
