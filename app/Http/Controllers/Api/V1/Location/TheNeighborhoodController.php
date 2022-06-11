<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location\The\TheNeighborhood;

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

            $theNeighborhoods = TheNeighborhood::whereRaw(
                "unaccent(name) ilike unaccent('%{$keyword}%')"
            )->limit(30)->get();
        } elseif ($request->has('type')) {
            $type = $request->get('type');
            if ($type == 'list') {
                $theNeighborhoods = TheNeighborhood::orderBy('the_neighborhoods.name')->get();
            } elseif ($type == 'geojson') {
                $theNeighborhoods = TheNeighborhood::select(
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
