<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\CampaignCycle;
use App\Models\ProfileWorker;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CampaignCyclePayrollExport;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;
use DateInterval;
use stdClass;

class CampaignCycleController extends Controller
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

        $cycles = CampaignCycle::orderBy('start', 'desc')->paginate($perPage);

        return $cycles;
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
            'number' => 'required',
            'description' => 'required',
            'start' => 'required',
            'campaign_id' => 'required',
        ]);

        $cycle = CampaignCycle::create([
            'number' => $request->number,
            'description' => $request->description,
            'start' => $request->start,
            'end' => $request->end,
            'campaign_id' => $request->campaign_id,
            'partial_value' => $request->partial_value,
            'percentage_value' => $request->percentage_value,
        ]);

        $cycle->save();

        $campaign = $cycle->campaign;

        $cycle->loadProfiles();

        foreach ($campaign->profiles as $profile) {
            $profile->loadWorkers($campaign->id, $cycle->id);
        }

        return $cycle;
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
                $cycle = CampaignCycle::with([
                    'supports.support' => function ($query) {
                        $query
                            ->selectRaw('vaccination_supports.*')
                            ->selectRaw(
                                'ST_AsGeoJSON(vaccination_supports.geometry) AS geometry'
                            );
                    },
                    'supports.support.neighborhoodAlias.neighborhood',
                ])->findOrFail($id);

                return $cycle;
            } elseif (strcmp($request->map, 'point') === 0) {
                $cycle = CampaignCycle::with([
                    'supports.support' => function ($query) {
                        $query
                            ->selectRaw('vaccination_supports.*')
                            ->selectRaw(
                                'ST_AsGeoJSON(vaccination_supports.geometry) AS geometry'
                            );
                    },
                    'supports.support.neighborhoodAlias.neighborhood',
                    'supports.points.point' => function ($query) {
                        $query
                            ->selectRaw('vaccination_points.*')
                            ->selectRaw(
                                'ST_AsGeoJSON(vaccination_points.geometry) AS geometry'
                            );
                    },
                    'supports.points.point.neighborhoodAlias.neighborhood',
                ])->findOrFail($id);

                return $cycle;
            }
        }
        $cycle = CampaignCycle::with([
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.supervisors',
            'supports.drivers',
            'supports.assistants',
            'supports.vaccinators',
            'supports.ruralSupervisors',
            'supports.ruralAssistants',
            'supports.saads',
        ])->findOrFail($id);

        foreach ($cycle->supports as $support) {
            $support->loadProfiles();
        }

        return $cycle;
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
        $cycle = CampaignCycle::findOrFail($id);
        $cycle->number = $request->number;
        $cycle->description = $request->description;
        $cycle->start = $request->start;
        $cycle->end = $request->end;
        $cycle->partial_value = $request->partial_value;
        $cycle->percentage_value = $request->percentage_value;

        $cycle->save();

        $campaign = $cycle->campaign;
        foreach ($request->profiles as $profile) {
            $p = $campaign
                ->profiles('cycle')
                ->orderBy('created_at', 'desc')
                ->find($profile['id']);

            $p->updateWorker($profile, $campaign->id, $cycle->id);
        }

        $cycle->loadProfiles();

        return $cycle;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cycle = CampaignCycle::with('supports.points')->findOrFail($id);

        foreach ($cycle->supports as $support) {
            foreach ($support->points as $point) {
                $point->delete();
            }
            $support->delete();
        }
        $cycle->delete();
    }

    public function allocation(Request $request, $id)
    {
        $today = date('d-m-Y');
        $dates = [];
        $cycle = CampaignCycle::with([
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.saads',
            'supports.points.point',
        ])->findOrFail($id);

        $profile = $cycle->profiles()->orderBy('is_pre_campaign', 'desc')->first();

        $lastDate = new DateTime($cycle->start);
        $dates[] = $lastDate->format('d/m/Y');

        for ($i=1; $i <= $profile->is_pre_campaign; $i++) {
            $lastDate->sub(new DateInterval('P1D'));
            $dates[$i] = $lastDate->format('d/m/Y');
        }

        $cycle->loadProfiles();

        foreach ($cycle->supports as $support) {
            $support->loadProfiles();

            foreach ($support->points as $point) {
                $point->loadProfiles();
            }
        }

        return PDF::loadView('ncrlo.location', [
            'cycle' => $cycle,
            'today' => $today,
            'dates' => $dates,
        ])->download("Relatório de Locação de Pessoal {$today}.pdf");
        //return view('receipt');
    }

    public function report(Request $request, $id)
    {
        $cycle = CampaignCycle::with([
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.saads',
            'supports.points.point',
        ])->findOrFail($id);

        $cycle->loadReport();
        return $cycle;
    }

    public function reportPdf(Request $request, $id)
    {
        $today = date('d-m-Y');
        $arraySaad = [];
        $cycle = CampaignCycle::with([
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.saads',
            'supports.points.point',
        ])->findOrFail($id);

        $cycle->loadReport();

        if ($request->has('details')) {
            return PDF::loadView('ncrlo.cycle_report_details', [
                'cycle' => $cycle,
                'today' => $today,
            ])->download("Relatório de Vacinação {$today}.pdf");
        } else {
            return PDF::loadView('ncrlo.cycle_report', [
                'cycle' => $cycle,
                'today' => $today,
            ])->download("Relatório de Vacinação {$today}.pdf");
        }

        //return view('receipt');
    }

    public function frequency(Request $request, $id)
    {
        $today = date('d-m-Y');
        $dates = [];
        $cycle = CampaignCycle::with([
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.saads',
            'supports.points.point',
        ])->findOrFail($id);

        $profile = $cycle->profiles()->orderBy('is_pre_campaign', 'desc')->first();

        $lastDate = new DateTime($cycle->start);
        $dates[] = $lastDate->format('d/m/Y');

        for ($i=1; $i <= $profile->is_pre_campaign; $i++) {
            $lastDate->sub(new DateInterval('P1D'));
            $dates[$i] = $lastDate->format('d/m/Y');
        }

        $cycle->loadProfiles();

        return PDF::loadView('ncrlo.frequency_list_cycle', [
            'cycle' => $cycle,
            'today' => $today,
            'dates' => $dates,
            'managements' => ['GEZOON', 'GETRANS', 'Rede de Frio']
        ])
            ->setPaper('a4', 'landscape')
            ->download("Frequência Locação de Pessoal {$today}.pdf");
        //return view('receipt');
    }

    public function payroll(Request $request, $id)
    {
        $today = date('d-m-Y');
        $dates = [];
        $cycle = CampaignCycle::select(
            'id',
            'number',
            'description',
            'start',
            'campaign_id'
        )->with([
                'supports' => function ($query) {
                    $query->select('id', 'campaign_cycle_id', 'is_rural');
                },
                'supports.points' => function ($query) {
                    $query->select('id', 'campaign_support_id');
                }
            ])->findOrFail($id);

        $profiles = ProfileWorker::where('is_pre_load', true)->get();
        $idsPreload = [];


        foreach ($profiles as $profile) {
            $idsPreload[] = $profile->id;
        }

        $profile = $cycle->profiles()->orderBy('is_pre_campaign', 'desc')->first();
        $lastDate = new DateTime($cycle->start);
        $dates[] = $lastDate->format('d/m/Y');

        for ($i=1; $i <= $profile->is_pre_campaign; $i++) {
            $lastDate->sub(new DateInterval('P1D'));
            $dates[$i] = $lastDate->format('d/m/Y');
        }

        $listWorkers = [];

        $findItemListWorkers = function ($item) use ($listWorkers) {
            foreach ($listWorkers as $key => $worker) {
                if (
                    $worker['profile_id'] == $item->profile_id &&
                    $worker['registration'] == $item->registration
                ) {
                    return $key;
                }
            }
            return false;
        };

        foreach ($dates as $key => $value) {
            $list = DB::table('campaign_worker')
            ->join('vaccination_workers', 'campaign_worker.vaccination_worker_id', '=', 'vaccination_workers.id')
            ->join('profile_workers', 'campaign_worker.profile_workers_id', '=', 'profile_workers.id')
            ->join('campaign_profile_workers', 'campaign_worker.profile_workers_id', '=', 'campaign_profile_workers.profile_workers_id')
            ->select(
                'vaccination_workers.registration as registration',
                'vaccination_workers.name as name',
                'profile_workers.name as profile',
                'profile_workers.id as profile_id',
                'campaign_profile_workers.cost as cost',
                'campaign_worker.is_pre_campaign'
            )->where('campaign_worker.campaign_cycle_id', $cycle->id)
            ->where('campaign_profile_workers.campaign_id', $cycle->campaign->id)
            ->where('campaign_worker.is_pre_campaign', $key)
            ->whereNotIn('campaign_worker.profile_workers_id', $idsPreload)
            ->orderBy('vaccination_workers.name', 'asc')
            ->get();
            if ($key === 0) {
                foreach ($list as $item) {
                    $listWorkers[] = [
                        'registration' => $item->registration,
                        'profile_id' => $item->profile_id,
                        'name' => $item->name,
                        'profile' => $item->profile,
                        'days' => [(float)$item->cost]
                    ];
                }
            } else {
                foreach ($list as $item) {
                    $index = $findItemListWorkers($item);
                    if ($index !== false) {
                        return $index;
                        $listWorkers[$index]['days'][$key] = (float)$item->cost;
                    } else {
                        $days = [];
                        for ($i=0; $i < $key; $i++) {
                            $days[$i] = 0.0;
                        }
                        $days[] = (float)$item->cost;

                        $listWorkers[] = [
                            'registration' => $item->registration,
                            'profile_id' => $item->profile_id,
                            'name' => $item->name,
                            'profile' => $item->name,
                            'days' => $days
                        ];
                    }
                }
            }
            for ($j = 0; $j < count($listWorkers); $j++) {
                if (!array_key_exists($key, $listWorkers[$j]['days'])) {
                    $listWorkers[$j]['days'][$key] = 0.0;
                }
            }
        }

        for ($i = 0; $i < count($listWorkers); $i++) {
            $total = 0;
            foreach ($listWorkers[$i]['days'] as $day) {
                $total += $day;
            }
            $listWorkers[$i]['total'] = $total;
        }

        $listProfile = DB::table('campaign_worker')
        ->join('profile_workers', 'campaign_worker.profile_workers_id', '=', 'profile_workers.id')
        ->join('campaign_profile_workers', 'campaign_worker.profile_workers_id', '=', 'campaign_profile_workers.profile_workers_id')
        ->select(
            DB::raw('count(campaign_worker.profile_workers_id) as count'),
            'campaign_worker.profile_workers_id as id',
            'profile_workers.name as profile',
            'profile_workers.management as management',
            'campaign_profile_workers.cost as cost',
        )->where('campaign_worker.campaign_cycle_id', $cycle->id)
        ->where('campaign_profile_workers.campaign_id', $cycle->campaign->id)
        ->whereNotIn('campaign_worker.profile_workers_id', $idsPreload)
        ->groupBy(
            'campaign_worker.profile_workers_id',
            'profile_workers.name',
            'campaign_profile_workers.cost',
            'profile_workers.management'
        )
        ->orderBy('count', 'desc')
        ->get();

        $total = 0;

        foreach ($listProfile as $item) {
            $item->total = $item->count * $item->cost;
            $total = $total + $item->total;
        }
        setlocale(LC_MONETARY, 'pt_BR');

        return PDF::loadView('ncrlo.payroll', [
            'cycle' => $cycle,
            'today' => $today,
            'dates' => $dates,
            'listWorkers' => $listWorkers,
            'listProfile' => $listProfile,
            'total' => $total,
        ])->setPaper('a4', 'landscape')->download("Folha de pagamento {$today}.pdf");
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function publicMap(Request $request, $id)
    {
        $cycle = CampaignCycle::with([
            'supports.support' => function ($query) {
                $query
                    ->selectRaw('vaccination_supports.*')
                    ->selectRaw(
                        'ST_AsGeoJSON(vaccination_supports.geometry) AS geometry'
                    );
            },
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.points.point' => function ($query) {
                $query
                    ->selectRaw('vaccination_points.*')
                    ->selectRaw(
                        'ST_AsGeoJSON(vaccination_points.geometry) AS geometry'
                    );
            },
            'supports.points.point.neighborhoodAlias.neighborhood',
        ])->findOrFail($id);

        return $cycle;
    }

    public function payrollCsv(Request $request, $id)
    {
        // return Excel::download(new CampaignCyclePayrollExport($id), 'payroll.csv', Excel::CSV);
        return (new CampaignCyclePayrollExport($id))->download(
            'payroll.csv',
            \Maatwebsite\Excel\Excel::CSV,
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }
}
