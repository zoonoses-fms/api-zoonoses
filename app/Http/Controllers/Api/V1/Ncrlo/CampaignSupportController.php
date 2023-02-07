<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\CampaignSupport;
use Illuminate\Http\Request;
use App\Models\ProfileWorker;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use DateInterval;

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

    public function frequencyBySupervisor(Request $request, $id)
    {
        $today = date('d-m-Y');

        $profiles = ProfileWorker::where('is_pre_load', true)->where('scope', 'point')->get();

        $idsPreload = [];

        foreach ($profiles as $profile) {
            $idsPreload[] = $profile->id;
        }

        $listSupervisors = DB::table('campaign_worker')
            ->join('vaccination_workers', 'campaign_worker.vaccination_worker_id', '=', 'vaccination_workers.id')
            ->select(
                'vaccination_workers.id as id',
                'vaccination_workers.registration as registration',
                'vaccination_workers.name as name',
                'vaccination_workers.phone as phone'
            )
            ->where('campaign_worker.campaign_support_id', $id)
            ->whereIn('campaign_worker.profile_workers_id', $idsPreload)
            ->groupBy(
                'vaccination_workers.id',
                'vaccination_workers.name'
            )
            ->orderBy('vaccination_workers.name')
            ->get();

        $listSupervisors;

        foreach ($listSupervisors as $supervisor) {
            $supervisor->points = DB::table('campaign_worker')
            ->join('campaign_points', 'campaign_worker.campaign_point_id', '=', 'campaign_points.id')
            ->join('vaccination_points', 'campaign_points.vaccination_point_id', '=', 'vaccination_points.id')

             ->select(
                 'campaign_worker.campaign_point_id as id',
                 'vaccination_points.name as name'
             )
             ->where('campaign_worker.campaign_support_id', $id)
             ->where('campaign_worker.vaccination_worker_id', $supervisor->id)
             ->whereIn('campaign_worker.profile_workers_id', $idsPreload)
             ->get();

            $idsPoints = [];

            foreach ($supervisor->points as $point) {
                $idsPoints[] = $point->id;
            }

            $supervisor->workers = DB::table('campaign_worker')
            ->join('vaccination_workers', 'campaign_worker.vaccination_worker_id', '=', 'vaccination_workers.id')
            ->join('profile_workers', 'campaign_worker.profile_workers_id', '=', 'profile_workers.id')
            ->select(
                'vaccination_workers.id as id',
                'vaccination_workers.registration as registration',
                'vaccination_workers.name as name',
                'vaccination_workers.phone as phone',
                'profile_workers.name as profile'
            )
            ->where('campaign_worker.campaign_support_id', $id)
            ->whereNotIn('campaign_worker.profile_workers_id', $idsPreload)
            ->whereIn('campaign_worker.campaign_point_id', $idsPoints)
            ->orderBy('vaccination_workers.name')
            ->get();
        }

        return PDF::loadView(
            'ncrlo.frequency_list_by_supervisor',
            [
                'supervisors' => $listSupervisors,
                'today' => $today,
            ]
        )->setPaper('a4', 'landscape')->download("Frequência Locação de Pessoal {$today}.pdf");
        //return view('receipt');
    }

    public function frequency(Request $request, $id)
    {
        $today = date('d-m-Y');
        $dates = [];
        $support = CampaignSupport::with([
            'support.neighborhoodAlias.neighborhood',
            'saads',
            'points.point',
        ])->findOrFail($id);

        $profile = $support->profiles()->orderBy('is_pre_campaign', 'desc')->first();

        $lastDate = new DateTime($support->cycle->start);
        $dates[] = $lastDate->format('d/m/Y');

        for ($i=1; $i <= $profile->is_pre_campaign; $i++) {
            $lastDate->sub(new DateInterval('P1D'));
            $dates[$i] = $lastDate->format('d/m/Y');
        }

        $support->loadProfiles();

        return PDF::loadView('ncrlo.frequency_list_support', [
            'support' => $support,
            'today' => $today,
            'dates' => $dates
        ])
            ->setPaper('a4', 'landscape')
            ->download("Frequência Locação de Pessoal {$today}.pdf");
        //return view('receipt');
    }
}
