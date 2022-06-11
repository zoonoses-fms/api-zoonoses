<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location\The\TheSaad;

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
            $theSaads = TheSaad::whereRaw(
                "unaccent(name) ilike unaccent('%{$keyword}%')"
            )->limit(30)->get();
        } elseif ($request->has('type')) {
            $type = $request->get('type');
            if ($type == 'list') {
                $theSaads = TheSaad::orderBy('the_saads.name')->get();
            } elseif ($type == 'geojson') {
                $theSaads = TheSaad::select(
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
