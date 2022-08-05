<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampaingPoint;

class CampaingPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $point = new CampaingPoint();
        $point->campaing_support_id = $request->campaing_support_id;
        $point->vaccination_point_id = $request->id;
        $point->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $point = CampaingPoint::with(
            [
                'supervisor',
                'point',
                'vaccinators'
            ]
        )->findOrFail($id);

        return $point;
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
        $point = CampaingPoint::findOrFail($id);
        $point->goal = $request->goal;
        $point->supervisor_id = $request->supervisor_id;
        $point->vaccinators()->sync($request->vaccinators);
        $point->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $point = CampaingPoint::with(
            [
                'vaccinators'
            ]
        )->findOrFail($id);

        $point->vaccinators()->sync([]);

        $point->delete();
    }
}
