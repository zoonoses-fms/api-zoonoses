<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\CampaignCycle;
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
            'campaign_id' => 'required'
        ]);

        $cycle = CampaignCycle::create([
            'number' => $request->number,
            'description' => $request->description,
            'start' => $request->start,
            'end' => $request->end,
            'campaign_id' => $request->campaign_id,
            'statistic_coordinator_id' => $request->statistic_coordinator_id,
            'cold_chain_coordinator_id' => $request->cold_chain_coordinator_id,
            'cold_chain_nurse_id' => $request->cold_chain_nurse_id,
            'partial_value' => $request->partial_value,
            'percentage_value' => $request->percentage_value
        ]);
        $cycle->statistics()->sync($request->statistics);

        $cycle->beforeTransports()->sync($request->before_transports);
        $cycle->startTransports()->sync($request->start_transports);

        $cycle->beforeColdChains()->sync($request->before_cold_chains);
        $cycle->startColdChains()->sync($request->start_cold_chains);

        $cycle->beforeDriverColdChains()->sync($request->before_driver_cold_chains);
        $cycle->startDriverColdChains()->sync($request->start_driver_cold_chains);

        $cycle->beforeZoonoses()->sync($request->before_zoonoses);
        $cycle->startZoonoses()->sync($request->start_zoonoses);

        $cycle->save();

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
                        $query->selectRaw(
                            'vaccination_supports.*'
                        )->selectRaw(
                            'ST_AsGeoJSON(vaccination_supports.geometry) AS geometry'
                        );
                    },
                    'supports.support.neighborhoodAlias.neighborhood'
                ])->findOrFail($id);

                return $cycle;
            } elseif (strcmp($request->map, 'point') === 0) {
                $cycle = CampaignCycle::with([
                    'supports.support' => function ($query) {
                        $query->selectRaw(
                            'vaccination_supports.*'
                        )->selectRaw(
                            'ST_AsGeoJSON(vaccination_supports.geometry) AS geometry'
                        );
                    },
                    'supports.support.neighborhoodAlias.neighborhood',
                    'supports.points.point' => function ($query) {
                        $query->selectRaw(
                            'vaccination_points.*'
                        )->selectRaw(
                            'ST_AsGeoJSON(vaccination_points.geometry) AS geometry'
                        );
                    },
                    'supports.points.point.neighborhoodAlias.neighborhood'
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
            'supports.saads'
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
        $cycle->statistic_coordinator_id = $request->statistic_coordinator_id;
        $cycle->cold_chain_coordinator_id = $request->cold_chain_coordinator_id;
        $cycle->cold_chain_nurse_id = $request->cold_chain_nurse_id;
        $cycle->partial_value = $request->partial_value;
        $cycle->percentage_value = $request->percentage_value;

        $cycle->save();

        $campaign = $cycle->campaign;
        foreach ($request->profiles as $profile) {
            $p =  $campaign->profiles('cycle')
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
        $today = date("d-m-Y");
        $cycle = CampaignCycle::with([
            'supports.coordinator',
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.supervisors',
            'supports.drivers',
            'supports.ruralSupervisors',
            'supports.ruralAssistants',
            'supports.assistants',
            'supports.vaccinators',
            'supports.ruralSupervisors',
            'supports.ruralAssistants',
            'supports.saads',
            'supports.points.point',
            'supports.points.supervisor',
            'supports.points.vaccinators',
            'supports.points.annotators',
        ])->findOrFail($id);

        return PDF::loadView(
            'ncrlo.location',
            [
                'cycle' => $cycle,
                'today' => $today,
            ]
        )->download("Relatório de Locação de Pessoal {$today}.pdf");
        //return view('receipt');
    }

    public function report(Request $request, $id)
    {
        $cycle = CampaignCycle::with([
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.saads',
            'supports.points.point'
        ])->findOrFail($id);

        $cycle->loadReport();
        return $cycle;
    }

    public function reportPdf(Request $request, $id)
    {
        $today = date("d-m-Y");
        $arraySaad = [];
        $cycle = CampaignCycle::with([
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.saads',
            'supports.points.point',
            'supports.points.supervisor',
            'supports.points.vaccinators',
            'supports.points.annotators',
        ])->findOrFail($id);

        $cycle->loadReport();

        if ($request->has('details')) {
            return PDF::loadView(
                'ncrlo.cycle_report_details',
                [
                    'cycle' => $cycle,
                    'today' => $today,
                ]
            )->download("Relatório de Vacinação {$today}.pdf");
        } else {
            return PDF::loadView(
                'ncrlo.cycle_report',
                [
                    'cycle' => $cycle,
                    'today' => $today,
                ]
            )->download("Relatório de Vacinação {$today}.pdf");
        }

        //return view('receipt');
    }

    public function frequency(Request $request, $id)
    {
        $today = new DateTime();
        $cycle = CampaignCycle::with([
            'coldChainCoordinator',
            'coldChainNurse',
            'beforeColdChains',
            'startColdChains',
            'beforeDriverColdChains',
            'startDriverColdChains',
            'statisticCoordinator',
            'statistics',
            'beforeTransports',
            'startTransports',
            'beforeZoonoses',
            'startZoonoses'

        ])->findOrFail($id);

        $start = new DateTime($cycle->start);
        $before = new DateTime($cycle->start);
        //Subtract a day using DateInterval
        $before->sub(new DateInterval('P1D'));

        //Get the date in a YYYY-MM-DD format.
        $before = $before->format('d/m/Y');
        $start = $start->format('d/m/Y');
        $currentDate = $today->format('d/m/Y');
        $today = $today->format('Y-m-d');

        return PDF::loadView(
            'ncrlo.frequency_list_support',
            [
                'cycle' => $cycle,
                'currentDate' => $currentDate,
                'start' => $start,
                'before' => $before,
            ]
        )->setPaper('a4', 'landscape')->download("Frequência Locação de Pessoal {$today}.pdf");
        //return view('receipt');
    }

    public function payroll(Request $request, $id)
    {
        $today = new DateTime();
        $cycle = CampaignCycle::with([
            'campaign',
            'coldChainCoordinator',
            'coldChainNurse',
            'beforeColdChains',
            'startColdChains',
            'beforeDriverColdChains',
            'startDriverColdChains',
            'statisticCoordinator',
            'statistics',
            'beforeTransports',
            'startTransports',
            'beforeZoonoses',
            'startZoonoses',
            'supports.coordinator',
            'supports.supervisors',
            'supports.assistants',
            'supports.drivers',
            'supports.vaccinators',
            'supports.ruralSupervisors',
            'supports.ruralAssistants',
            'supports.points.vaccinators',
            'supports.points.annotators',

        ])->findOrFail($id);

        // return $cycle;

        $start = new DateTime($cycle->start);
        $before = new DateTime($cycle->start);
        //Subtract a day using DateInterval
        $before->sub(new DateInterval('P1D'));

        //Get the date in a YYYY-MM-DD format.
        $before = $before->format('d/m/Y');
        $start = $start->format('d/m/Y');
        $currentDate = $today->format('d/m/Y');
        $today = $today->format('Y-m-d');

        $payroll = $cycle->getPayroll();

        $total = $payroll['total'];

        $count = $payroll['count'];

        setlocale(LC_MONETARY, 'pt_BR');

        return PDF::loadView(
            'ncrlo.payroll',
            [
                'cycle' => $cycle,
                'currentDate' => $currentDate,
                'start' => $start,
                'before' => $before,
                'total' => $total,
                'count' => $count,
            ]
        )->setPaper('a4', 'landscape')->download("Folha de pagamento {$today}.pdf");
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
                $query->selectRaw(
                    'vaccination_supports.*'
                )->selectRaw(
                    'ST_AsGeoJSON(vaccination_supports.geometry) AS geometry'
                );
            },
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.points.point' => function ($query) {
                $query->selectRaw(
                    'vaccination_points.*'
                )->selectRaw(
                    'ST_AsGeoJSON(vaccination_points.geometry) AS geometry'
                );
            },
            'supports.points.point.neighborhoodAlias.neighborhood'
        ])->findOrFail($id);

        return $cycle;
    }

    public function payrollCsv(Request $request, $id)
    {
        // return Excel::download(new CampaignCyclePayrollExport($id), 'payroll.csv', Excel::CSV);
        return (new CampaignCyclePayrollExport($id))->download('payroll.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
