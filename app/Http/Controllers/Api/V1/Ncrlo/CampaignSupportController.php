<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\CampaignSupport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CampaignSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('campaign_support_id')) {

            $support = CampaignSupport::find($request->campaign_support_id);
            $cycle = $support->cycle;
            $supports = $cycle->supports()->with('support')->get();
            return $supports;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $support = new CampaignSupport();
        $support->campaign_cycle_id = $request->campaign_cycle_id;
        $support->vaccination_support_id = $request->id;
        $support->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $support = CampaignSupport::with(
            [
                'cycle',
                'support',
                'coordinator',
                'supervisors',
                'drivers',
                'assistants',
                'vaccinators',
                'saads',
                'points' => function ($q) {
                    $q->orderBy('area')->orderBy('order');
                },
                'points.point.neighborhoodAlias.neighborhood',
                'points.vaccinators',
                'points.annotators'
            ]
        )->findOrFail($id);

        $support->loadProfiles();

        foreach ($support->points as $point) {
            $point->loadProfiles();
        }

        return $support;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $support = CampaignSupport::findOrFail($id);
        $support->order = $request->order;
        $support->goal = $request->goal;
        $support->mileage = $request->mileage;
        $support->coordinator_id = $request->coordinator_id;

        $support->save();

        $cycle = $support->cycle;
        $campaign = $cycle->campaign;
        foreach ($request->profiles as $profile) {
            $p =  $campaign->profiles('support')
                ->where('is_rural', $support->is_rural)
                ->orderBy('created_at', 'desc')
                ->find($profile['id']);

            $p->updateWorker($profile, $campaign->id, $cycle->id, $support->id);
        }

        $support->loadProfiles();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $support = CampaignSupport::with([
            'cycle',
            'support',
            'coordinator',
            'supervisors',
            'drivers',
            'points.point',
            'points.vaccinators'
        ])->findOrFail($id);

        foreach ($support->points as $point) {
            $point->delete();
        }

        $support->supervisors()->sync([]);
        $support->drivers()->sync([]);
        $support->assistants()->sync([]);
        $support->saads()->sync([]);
        $support->ruralSupervisors()->sync([]);
        $support->ruralAssistants()->sync([]);

        $support->delete();
    }

    public function frequency(Request $request, $id)
    {
        $today = date("d-m-Y");
        $support = CampaignSupport::with([
            'coordinator',
            'support.neighborhoodAlias.neighborhood',
            'supervisors',
            'drivers',
            'ruralSupervisors',
            'ruralAssistants',
            'assistants',
            'vaccinators',
            'ruralSupervisors',
            'ruralAssistants',
            'saads',
            'points' => function ($q) {
                $q->orderBy('area')->orderBy('order');
            },
            'points.point',
            'points.supervisor',
            'points.vaccinators',
            'points.annotators',
        ])->findOrFail($id);

        return PDF::loadView(
            'ncrlo.frequency_list',
            [
                'support' => $support,
                'today' => $today,
            ]
        )->setPaper('a4', 'landscape')->download("Frequência Locação de Pessoal {$today}.pdf");
        //return view('receipt');
    }
}