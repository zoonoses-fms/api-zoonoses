<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampaignPoint;
use Barryvdh\DomPDF\Facade\Pdf;

class CampaignPointController extends Controller
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
        $point = new CampaignPoint();
        $point->campaign_support_id = $request->campaign_support_id;
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
        $point = CampaignPoint::with(
            [
                'supervisor',
                'point',
                'vaccinators',
                'annotators'
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
        $point = CampaignPoint::findOrFail($id);
        $point->order = $request->order;
        $point->area = $request->area;
        $point->goal = $request->goal;
        $point->male_dog_under_4m = $request->male_dog_under_4m;
        $point->female_dog_under_4m = $request->female_dog_under_4m;
        $point->male_dog_major_4m_under_1y = $request->male_dog_major_4m_under_1y;
        $point->female_dog_major_4m_under_1y = $request->female_dog_major_4m_under_1y;
        $point->male_dog_major_1y_under_2y = $request->male_dog_major_1y_under_2y;
        $point->female_dog_major_1y_under_2y = $request->female_dog_major_1y_under_2y;
        $point->male_dog_major_2y_under_4y = $request->male_dog_major_2y_under_4y;
        $point->female_dog_major_2y_under_4y = $request->female_dog_major_2y_under_4y;
        $point->male_dog_major_4y = $request->male_dog_major_4y;
        $point->female_dog_major_4y = $request->female_dog_major_4y;
        $point->male_dogs = $request->male_dogs;
        $point->female_dogs = $request->female_dogs;
        $point->total_of_dogs = $request->total_of_dogs;
        $point->male_cat = $request->male_cat;
        $point->female_cat = $request->female_cat;
        $point->total_of_cats = $request->total_of_cats;
        $point->total = $request->total;
        $point->bottle_received = $request->bottle_received;
        $point->bottle_used_completely = $request->bottle_used_completely;
        $point->bottle_used_partially = $request->bottle_used_partially;
        $point->bottle_returned_completely = $request->bottle_returned_completely;
        $point->bottle_returned_partially = $request->bottle_returned_partially;
        $point->bottle_lost = $request->bottle_lost;

        $point->supervisor_id = $request->supervisor_id;
        $point->save();

        $support = $point->support;
        $cycle = $support->cycle;
        $campaign = $cycle->campaign;
        foreach ($request->profiles as $profile) {
            $p =  $campaign->profiles('point')
                ->orderBy('created_at', 'desc')
                ->find($profile['id']);

            $p->updateWorker($profile, $campaign->id, $cycle->id, $support->id, $point->id);
        }

        $point->loadProfiles();

        return $point;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $point = CampaignPoint::with(
            [
                'vaccinators'
            ]
        )->findOrFail($id);

        $point->vaccinators()->sync([]);

        $point->delete();
    }

    public function frequency(Request $request, $id)
    {
        $today = date("d-m-Y");
        $point = CampaignPoint::with([
            'point',
            'supervisor',
            'vaccinators',
            'annotators',
        ])->findOrFail($id);

        return PDF::loadView(
            'ncrlo.frequency_point_list',
            [
                'point' => $point,
                'today' => $today,
            ]
        )->setPaper('a4', 'landscape')->download("Frequência Locação de Pessoal {$today}.pdf");
        //return view('receipt');
    }
}
