<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignProfileWorker;
use App\Models\ProfileWorker;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Psy\Sudo;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use DateInterval;

class CampaignController extends Controller
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

        $campaigns = Campaign::with([
            'profiles' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'profilesAll' => function ($query) {
                $query->withPivot('id', 'cost')->orderBy('created_at', 'asc');
            },
        ])->orderBy('year', 'desc')->paginate($perPage);

        foreach ($campaigns as $campaign) {
            foreach ($campaign->profiles as $profile) {
                $profile->loadWorkers([$campaign->id]);
            }
        }

        return $campaigns;
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


        $campaign = Campaign::create([
            'year' => $request->year,
            'start' => $request->start,
            'end' => $request->end,
            'goal' => $request->goal,
        ]);

        return $campaign;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->has('map')) {
            if (strcmp($request->map, 'support') === 0) {
                $campaign = Campaign::with([
                    'cycles.supports.support' => function ($query) {
                        $query->selectRaw(
                            'vaccination_supports.*'
                        )->selectRaw(
                            'ST_AsGeoJSON(vaccination_supports.geometry) AS geometry'
                        );
                    }
                ])->findOrFail($id);

                return $campaign;
            } elseif (strcmp($request->map, 'point') === 0) {
                $campaign = Campaign::with([
                    'cycles.supports.points.point' => function ($query) {
                        $query->selectRaw(
                            'vaccination_points.*'
                        )->selectRaw(
                            'ST_AsGeoJSON(vaccination_points.geometry) AS geometry'
                        );
                    }
                ])->findOrFail($id);

                return $campaign;
            }
        }
        $campaign = Campaign::with([
            'cycles.statistics',
            'cycles.beforeTransports',
            'cycles.startTransports',
            'cycles.beforeColdChains',
            'cycles.startColdChains',
            'cycles.beforeDriverColdChains',
            'cycles.startDriverColdChains',
            'cycles.beforeZoonoses',
            'cycles.startZoonoses'
        ])->findOrFail($id);

        foreach ($campaign->cycles as $cycle) {
            $cycle->loadProfiles();
        }

        return $campaign;
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
        $campaign = Campaign::findOrFail($id);
        $campaign->year = $request->year;
        $campaign->start = $request->start;
        $campaign->end = $request->end;
        $campaign->goal = $request->goal;


        $profiles = [];
        foreach ($request->profiles_all as $profile) {
            $profiles[$profile['id']] = [
                'cost' => $profile['pivot']['cost'],
                'updated_at' => now()
            ];
        }
        $campaign->profilesAll()->sync($profiles);

        foreach ($request->profiles as $profile) {

            $p = $campaign->profiles->find($profile['id']);

            $p->updateWorker($profile, $campaign->id);
        }

        $campaign->save();

        $campaign->load('profiles');

        foreach ($campaign->profiles as $profile) {
            $profile->loadWorkers($campaign->id);
        }

        return $campaign;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $campaign = Campaign::with('cycles.supports.points')->findOrFail($id);

        foreach ($campaign->cycles as $cycle) {
            foreach ($cycle->supports as $support) {
                foreach ($support->points as $point) {
                    $point->delete();
                }
                $support->delete();
            }
            $cycle->delete();
        }
        $campaign->delete();
    }

    public function report(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        Campaign::buildItem($campaign);
        foreach ($campaign->cycles as $cycle) {
            $cycle->loadReport();
            Campaign::incrementItem($campaign, $cycle);
        }

        return $campaign;
    }

    public function reportPdf(Request $request, $id)
    {
        $today = date("d-m-Y");
        $arraySaad = [];
        $campaign = Campaign::findOrFail($id);

        Campaign::buildItem($campaign);
        foreach ($campaign->cycles as $cycle) {
            $cycle->loadReport();
            Campaign::incrementItem($campaign, $cycle);
        }

        return PDF::loadView(
            'ncrlo.campaign_report',
            [
                'campaign' => $campaign,
                'today' => $today,
            ]
        )->setPaper('a4', 'landscape')->download("Relatório de Vacinação {$today}.pdf");


        //return view('receipt');
    }

    public function payroll(Request $request, $id)
    {
        $today = date('d-m-Y');
        $campaign = Campaign::find($id);

        $profiles = ProfileWorker::where('is_pre_load', true)->get();
        $idsPreload = [];


        foreach ($profiles as $profile) {
            $idsPreload[] = $profile->id;
        }

        $total = 0;

        $cycles = [];

        foreach ( $campaign->cycles as $cycle) {

            $listProfile = DB::table('campaign_worker')
            ->join('profile_workers', 'campaign_worker.profile_workers_id', '=', 'profile_workers.id')
            ->join('campaign_profile_workers', 'campaign_worker.profile_workers_id', '=', 'campaign_profile_workers.profile_workers_id')
            ->select(
                DB::raw('count(campaign_worker.profile_workers_id) as count'),
                'campaign_worker.profile_workers_id as id',
                'profile_workers.name as profile',
                'profile_workers.management as management',
                'campaign_profile_workers.cost as cost',
            )
            ->where('campaign_worker.campaign_cycle_id', $cycle->id)
            ->where('campaign_worker.campaign_id', $campaign->id)
            ->where('campaign_profile_workers.campaign_id', $campaign->id)
            ->whereNotIn('campaign_worker.profile_workers_id', $idsPreload)
            ->groupBy(
                'campaign_worker.profile_workers_id',
                'profile_workers.name',
                'campaign_profile_workers.cost',
                'profile_workers.management'
            )
            ->orderBy('count', 'desc')
            ->get();

            $cycle->total = 0;

            foreach ($listProfile as $item) {
                $item->total = $item->count * $item->cost;
                $cycle->total += $item->total;
                $total = $total + $item->total;
            }

        }


        setlocale(LC_MONETARY, 'pt_BR');

        return PDF::loadView('ncrlo.campaign_payroll', [
            'campaign' => $campaign,
            'today' => $today,
            'listProfile' => $listProfile,
            'total' => $total,
        ])->setPaper('a4', 'landscape')->download("Folha de pagamento {$today}.pdf");
    }
}
