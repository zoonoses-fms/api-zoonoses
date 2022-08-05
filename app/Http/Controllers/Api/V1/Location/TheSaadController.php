<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location\The\TheSaad;
use App\Models\Location\The\TheSaadGeography;
use Illuminate\Support\Facades\DB;

class TheSaadController extends Controller
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
            if (empty($keyword)) {
                $theSaads = TheSaad::get();
            } else {
                $theSaads = TheSaad::searchByName($keyword);
            }
        } elseif ($request->has('type')) {
            $type = $request->get('type');
            if ($type == 'list') {
                $theSaads = TheSaad::orderBy('the_saads.name')->get();
            } elseif ($type == 'geojson') {
                $theSaads = TheSaad::getGeoJSON($request);
            }
        } else {
            if ($request->has('per_page')) {
                $perPage = $request->input('per_page');
            } else {
                $perPage = 10;
            }

            $theSaads = TheSaad::paginate($perPage);
        }


        return $theSaads;
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

            $lastSaad = TheSaad::orderBy('gid', 'desc')->first();

            $saad = new TheSaad();
            $name = trim(str_replace('SAAD ', '', $feature['properties']['name']));
            $saad->name = $name;
            $saad->standardized = $saad->nameCase($name);
            $saad->metaphone = $saad->getPhraseMetaphone($name);
            $saad->soundex = soundex($name);
            $saad->gid = $lastSaad != null ? $lastSaad->gid + 1 : 1;
            $saadGeography = new TheSaadGeography();
            $geometry = json_encode($feature['geometry']);
            $saadGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
            $saad->save();
            $saad->geography()->save($saadGeography);

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

            $lastSaad = TheSaad::orderBy('gid', 'desc')->first();

            $saad = TheSaad::find($id);
            $name = trim(str_replace('SAAD ', '', $feature['properties']['name']));
            $saad->name = $name;
            $saad->standardized = $saad->nameCase($name);
            $saad->metaphone = $saad->getPhraseMetaphone($name);
            $saad->soundex = soundex($name);
            $saadGeography = $saad->geography;
            $geometry = json_encode($feature['geometry']);
            $saadGeography->area = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
            $saad->save();
            $saadGeography->save();

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
        $saad = TheSaad::find($id);
        $saadGeography = $saad->geography;
        $saadGeography->delete();
        $saad->delete();
    }
}
