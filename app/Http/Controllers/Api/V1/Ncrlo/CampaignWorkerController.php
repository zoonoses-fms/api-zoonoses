<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\ProfileWorker;
use Illuminate\Http\Request;
use App\Models\CampaignWorkers;

class CampaignWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('campaign_id') && $request->has('campaign_cycle_id')) {

            if ($request->scope == 'support') {
                $workes = CampaignWorkers::with('profile', 'worker', 'support.support', 'support.saads')->where(
                    'campaign_id',
                    $request->campaign_id
                )->where(
                    'campaign_cycle_id',
                    $request->campaign_cycle_id
                )->whereNotNull(
                    'campaign_support_id'
                )->whereNull(
                    'campaign_point_id'
                )->get();

                return $workes;
            }

            if ($request->scope == 'point') {

                $profile = ProfileWorker::where('name', 'Supervisor de Posto')->where('scope', 'point')->first();
                $workes = CampaignWorkers::with(
                    'profile',
                    'worker',
                    'support.support',
                    'support.saads',
                    'point.point'
                )->where(
                    'campaign_id',
                    $request->campaign_id
                )->where(
                    'campaign_cycle_id',
                    $request->campaign_cycle_id
                )->where(
                    'profile_workers_id',
                    '!=',
                    $profile->id
                )->whereNotNull(
                    'campaign_support_id'
                )->whereNotNull(
                    'campaign_point_id'
                )->get();

                return $workes;
            }



        }

        $workes = CampaignWorkers::get();

        return $workes;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        $worker = CampaignWorkers::findOrFail($id);
        if ($request->has('is_confirmation')) {
            $worker->is_confirmation = $request->is_confirmation == "true" ? true : false;
            $worker->save();
            return $worker;
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
        //
    }
}
