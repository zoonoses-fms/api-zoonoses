<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location\The\TheSubLocation;
use App\Models\Location\The\TheSubLocationGeography;
use App\Models\Location\The\TheSaad;
use Illuminate\Support\Facades\DB;

class TheSubLoacationController extends Controller
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
            $theSublocations = TheSubLocation::searchByName($keyword);
        } elseif ($request->has('type')) {

            $type = $request->get('type');
            if ($type == 'list') {
                $theSublocations = TheSaad::orderBy('the_saads.name')->with('neighborhoods')->get();
            } elseif ($type == 'geojson') {
                $theSublocations = TheSubLocation::getGeoJSON($request);
            }
        } else {
            if ($request->has('per_page')) {
                $perPage = $request->input('per_page');
            } else {
                $perPage = 10;
            }

            $theSublocations = TheSubLocation::paginate($perPage);
        }

        return $theSublocations;
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

            $lastSubLocation  = TheSubLocation::orderBy('gid', 'desc')->first();

            $subLocation = new TheSubLocation();
            $name = trim($feature['properties']['name']);
            $subLocation->name = $name;
            $subLocation->standardized = $subLocation->nameCase($name);
            $subLocation->metaphone = $subLocation->getPhraseMetaphone($name);
            $subLocation->soundex = soundex($name);
            $subLocation->gid = $lastSubLocation != null ? $lastSubLocation->gid + 1 : 1;
            $subLocation->the_neighborhood_id = $feature['properties']['feature_id'];
            $subLocationGeography = new TheSubLocationGeography();
            $geometry = json_encode($feature['geometry']);
            $subLocationGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
            $subLocation->save();
            $subLocation->geography()->save($subLocationGeography);

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

            $subLocation = TheSubLocation::find($id);
            $name = trim($feature['properties']['name']);
            $subLocation->name = $name;
            $subLocation->standardized = $subLocation->nameCase($name);
            $subLocation->metaphone = $subLocation->getPhraseMetaphone($name);
            $subLocation->soundex = soundex($name);
            $subLocation->the_neighborhood_id = $feature['properties']['feature_id'];
            $subLocationGeography = $subLocation->geography;
            $geometry = json_encode($feature['geometry']);
            $subLocationGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
            $subLocation->save();
            $subLocationGeography->save();

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
        $subLocation = TheSubLocation::find($id);
        $subLocation->geography->delete();
        $subLocation->delete();
    }
}
