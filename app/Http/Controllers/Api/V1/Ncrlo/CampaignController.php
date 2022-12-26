<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignProfileWorker;
use Illuminate\Contracts\Auth\SupportsBasicAuth;
use Illuminate\Http\Request;
use Psy\Sudo;
use Barryvdh\DomPDF\Facade\Pdf;

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

            'coordinator_cost' => $request->coordinator_cost,
            'supervisor_cost' => $request->supervisor_cost,
            'assistant_cost' => $request->assistant_cost,
            'vaccinator_cost' => $request->vaccinator_cost,
            'annotators_cost' => $request->annotators_cost,

            'rural_supervisor_cost' => $request->rural_supervisor_cost,
            'rural_assistant_cost' => $request->rural_assistant_cost,

            'vaccine_cost' => $request->vaccine_cost,
            'mileage_cost' => $request->mileage_cost,
            'driver_cost' => $request->driver_cost,
            'coordinator_id' => $request->coordinator_id,

            'statistic_coordinator_cost' => $request->statistic_coordinator_cost,
            'cold_chain_coordinator_cost' => $request->cold_chain_coordinator_cost,
            'cold_chain_nurse_cost' => $request->cold_chain_nurse_cost,
            'statistic_cost' => $request->statistic_cost,
            'cold_chain_cost' => $request->cold_chain_cost,
            'zoonoses_cost' => $request->zoonoses_cost,
            'transport_cost' => $request->transport_cost,

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

        $campaign->coordinator_cost = $request->coordinator_cost;
        $campaign->supervisor_cost = $request->supervisor_cost;
        $campaign->assistant_cost = $request->assistant_cost;
        $campaign->vaccinator_cost = $request->vaccinator_cost;
        $campaign->annotators_cost = $request->annotators_cost;

        $campaign->rural_supervisor_cost = $request->rural_supervisor_cost;
        $campaign->rural_assistant_cost = $request->rural_assistant_cost;

        $campaign->vaccine_cost = $request->vaccine_cost;
        $campaign->mileage_cost = $request->mileage_cost;
        $campaign->driver_cost = $request->driver_cost;
        $campaign->coordinator_id = $request->coordinator_id;

        $campaign->statistic_coordinator_cost = $request->statistic_coordinator_cost;
        $campaign->cold_chain_coordinator_cost = $request->cold_chain_coordinator_cost;
        $campaign->cold_chain_nurse_cost = $request->cold_chain_nurse_cost;
        $campaign->statistic_cost = $request->statistic_cost;
        $campaign->cold_chain_cost = $request->cold_chain_cost;
        $campaign->zoonoses_cost = $request->zoonoses_cost;
        $campaign->transport_cost = $request->transport_cost;

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
}
