<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\VaccinationCampaign;
use Illuminate\Contracts\Auth\SupportsBasicAuth;
use Illuminate\Http\Request;
use Psy\Sudo;

class VaccinationCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('per_page')) {
            $perPage = $request->input('per_page');
        } else {
            $perPage = 5;
        }

        $vaccinationCampaigns = VaccinationCampaign::orderBy('year', 'desc')->paginate($perPage);

        return $vaccinationCampaigns;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'year' => 'required',
            'start' => 'required'
        ]);

        $vaccinationCampaign = VaccinationCampaign::create([
            'year' => $request->year,
            'start' => $request->start,
            'end' => $request->end,
            'goal' => $request->goal,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VaccinationCampaign  $vaccinationCampaign
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vaccinationCampaign = VaccinationCampaign::with('supports.support')->findOrFail($id);

        return $vaccinationCampaign;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VaccinationCampaign  $vaccinationCampaign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VaccinationCampaign $vaccinationCampaign)
    {
        $vaccinationCampaign->year = $request->year;
        $vaccinationCampaign->start = $request->start;
        $vaccinationCampaign->end = $request->end;
        $vaccinationCampaign->goal = $request->goal;

        $vaccinationCampaign->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VaccinationCampaign  $vaccinationCampaign
     * @return \Illuminate\Http\Response
     */
    public function destroy(VaccinationCampaign $vaccinationCampaign)
    {
        $vaccinationCampaign->delete();
        
    }
}
