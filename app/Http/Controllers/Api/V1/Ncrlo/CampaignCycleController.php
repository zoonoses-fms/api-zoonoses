<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\CampaignCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
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
            'cold_chain_nurse_id' => $request->cold_chain_nurse_id
        ]);
        $cycle->statistics()->sync($request->statistics);
        $cycle->transports()->sync($request->transports);
        $cycle->beforeColdChains()->sync($request->before_cold_chains);
        $cycle->startColdChains()->sync($request->start_cold_chains);
        $cycle->driverColdChains()->sync($request->driver_cold_chains);
        $cycle->zoonoses()->sync($request->zoonoses);

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
                    }
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
                    'supports.points.point' => function ($query) {
                        $query->selectRaw(
                            'vaccination_points.*'
                        )->selectRaw(
                            'ST_AsGeoJSON(vaccination_points.geometry) AS geometry'
                        );
                    }
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
        $cycle->statistics()->sync($request->statistics);
        $cycle->transports()->sync($request->transports);
        $cycle->beforeColdChains()->sync($request->before_cold_chains);
        $cycle->startColdChains()->sync($request->start_cold_chains);
        $cycle->driverColdChains()->sync($request->driver_cold_chains);
        $cycle->zoonoses()->sync($request->zoonoses);

        $cycle->save();

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

        $cycle->male_dog_under_4m = 0;
        $cycle->female_dog_under_4m = 0;

        $cycle->male_dog_major_4m_under_1y = 0;
        $cycle->female_dog_major_4m_under_1y = 0;

        $cycle->male_dog_major_1y_under_2y = 0;
        $cycle->female_dog_major_1y_under_2y = 0;

        $cycle->male_dog_major_2y_under_4y = 0;
        $cycle->female_dog_major_2y_under_4y = 0;

        $cycle->male_dog_major_4y = 0;
        $cycle->female_dog_major_4y = 0;

        $cycle->male_dogs = 0;
        $cycle->female_dogs = 0;

        $cycle->total_of_dogs = 0;

        $cycle->male_cat = 0;
        $cycle->female_cat = 0;

        $cycle->total_of_cats = 0;
        $cycle->total = 0;
        $cycle->goal = 0;

        foreach ($cycle->supports as $support) {
            $support->male_dog_under_4m = 0;
            $support->female_dog_under_4m = 0;

            $support->male_dog_major_4m_under_1y = 0;
            $support->female_dog_major_4m_under_1y = 0;

            $support->male_dog_major_1y_under_2y = 0;
            $support->female_dog_major_1y_under_2y = 0;

            $support->male_dog_major_2y_under_4y = 0;
            $support->female_dog_major_2y_under_4y = 0;

            $support->male_dog_major_4y = 0;
            $support->female_dog_major_4y = 0;

            $support->male_dogs = 0;
            $support->female_dogs = 0;

            $support->total_of_dogs = 0;

            $support->male_cat = 0;
            $support->female_cat = 0;

            $support->total_of_cats = 0;
            $support->total = 0;
            $support->goal = 0;

            $indexSaad = null;

            foreach ($arraySaad as $saad) {
                if ($support->saads[0]->id == $saad->id) {
                    $indexSaad = $saad->id;
                    break;
                }
            }

            if ($indexSaad == null) {
                $saad = new stdClass();
                $saad->id = $support->saads[0]->id;
                $saad->name = $support->saads[0]->name;
                $indexSaad = $support->saads[0]->id;

                $arraySaad[$indexSaad] = $saad;

                $arraySaad[$indexSaad]->male_dog_under_4m = 0;
                $arraySaad[$indexSaad]->female_dog_under_4m = 0;

                $arraySaad[$indexSaad]->male_dog_major_4m_under_1y = 0;
                $arraySaad[$indexSaad]->female_dog_major_4m_under_1y = 0;

                $arraySaad[$indexSaad]->male_dog_major_1y_under_2y = 0;
                $arraySaad[$indexSaad]->female_dog_major_1y_under_2y = 0;

                $arraySaad[$indexSaad]->male_dog_major_2y_under_4y = 0;
                $arraySaad[$indexSaad]->female_dog_major_2y_under_4y = 0;

                $arraySaad[$indexSaad]->male_dog_major_4y = 0;
                $arraySaad[$indexSaad]->female_dog_major_4y = 0;

                $arraySaad[$indexSaad]->male_dogs = 0;
                $arraySaad[$indexSaad]->female_dogs = 0;

                $arraySaad[$indexSaad]->total_of_dogs = 0;

                $arraySaad[$indexSaad]->male_cat = 0;
                $arraySaad[$indexSaad]->female_cat = 0;

                $arraySaad[$indexSaad]->total_of_cats = 0;
                $arraySaad[$indexSaad]->total = 0;
                $arraySaad[$indexSaad]->goal = 0;
            }

            foreach ($support->points as $point) {
                $support->male_dog_under_4m += $point->male_dog_under_4m;
                $support->female_dog_under_4m += $point->female_dog_under_4m;

                $support->male_dog_major_4m_under_1y += $point->male_dog_major_4m_under_1y;
                $support->female_dog_major_4m_under_1y += $point->female_dog_major_4m_under_1y;

                $support->male_dog_major_1y_under_2y += $point->male_dog_major_1y_under_2y;
                $support->female_dog_major_1y_under_2y += $point->female_dog_major_1y_under_2y;

                $support->male_dog_major_2y_under_4y += $point->male_dog_major_2y_under_4y;
                $support->female_dog_major_2y_under_4y += $point->female_dog_major_2y_under_4y;

                $support->male_dog_major_4y += $point->male_dog_major_4y;
                $support->female_dog_major_4y += $point->female_dog_major_4y;

                $support->male_dogs += $point->male_dogs;
                $support->female_dogs += $point->female_dogs;

                $support->total_of_dogs += $point->total_of_dogs;

                $support->male_cat += $point->male_cat;
                $support->female_cat += $point->female_cat;

                $support->total_of_cats += $point->total_of_cats;
                $support->total += $point->total;
                $support->goal += $point->goal;
            }

            $arraySaad[$indexSaad]->male_dog_under_4m += $support->male_dog_under_4m;
            $arraySaad[$indexSaad]->female_dog_under_4m += $support->female_dog_under_4m;

            $arraySaad[$indexSaad]->male_dog_major_4m_under_1y += $support->male_dog_major_4m_under_1y;
            $arraySaad[$indexSaad]->female_dog_major_4m_under_1y += $support->female_dog_major_4m_under_1y;

            $arraySaad[$indexSaad]->male_dog_major_1y_under_2y += $support->male_dog_major_1y_under_2y;
            $arraySaad[$indexSaad]->female_dog_major_1y_under_2y += $support->female_dog_major_1y_under_2y;

            $arraySaad[$indexSaad]->male_dog_major_2y_under_4y += $support->male_dog_major_2y_under_4y;
            $arraySaad[$indexSaad]->female_dog_major_2y_under_4y += $support->female_dog_major_2y_under_4y;

            $arraySaad[$indexSaad]->male_dog_major_4y += $support->male_dog_major_4y;
            $arraySaad[$indexSaad]->female_dog_major_4y += $support->female_dog_major_4y;

            $arraySaad[$indexSaad]->male_dogs += $support->male_dogs;
            $arraySaad[$indexSaad]->female_dogs += $support->female_dogs;

            $arraySaad[$indexSaad]->total_of_dogs += $support->total_of_dogs;

            $arraySaad[$indexSaad]->male_cat += $support->male_cat;
            $arraySaad[$indexSaad]->female_cat += $support->female_cat;

            $arraySaad[$indexSaad]->total_of_cats += $support->total_of_cats;
            $arraySaad[$indexSaad]->total += $support->total;
            $arraySaad[$indexSaad]->goal += $support->goal;

            $cycle->male_dog_under_4m += $support->male_dog_under_4m;
            $cycle->female_dog_under_4m += $support->female_dog_under_4m;

            $cycle->male_dog_major_4m_under_1y += $support->male_dog_major_4m_under_1y;
            $cycle->female_dog_major_4m_under_1y += $support->female_dog_major_4m_under_1y;

            $cycle->male_dog_major_1y_under_2y += $support->male_dog_major_1y_under_2y;
            $cycle->female_dog_major_1y_under_2y += $support->female_dog_major_1y_under_2y;

            $cycle->male_dog_major_2y_under_4y += $support->male_dog_major_2y_under_4y;
            $cycle->female_dog_major_2y_under_4y += $support->female_dog_major_2y_under_4y;

            $cycle->male_dog_major_4y += $support->male_dog_major_4y;
            $cycle->female_dog_major_4y += $support->female_dog_major_4y;

            $cycle->male_dogs += $support->male_dogs;
            $cycle->female_dogs += $support->female_dogs;

            $cycle->total_of_dogs += $support->total_of_dogs;

            $cycle->male_cat += $support->male_cat;
            $cycle->female_cat += $support->female_cat;

            $cycle->total_of_cats += $support->total_of_cats;
            $cycle->total += $support->total;
            $cycle->goal += $support->goal;
        }

        $cycle->saads = $arraySaad;
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

        $cycle->male_dog_under_4m = 0;
        $cycle->female_dog_under_4m = 0;

        $cycle->male_dog_major_4m_under_1y = 0;
        $cycle->female_dog_major_4m_under_1y = 0;

        $cycle->male_dog_major_1y_under_2y = 0;
        $cycle->female_dog_major_1y_under_2y = 0;

        $cycle->male_dog_major_2y_under_4y = 0;
        $cycle->female_dog_major_2y_under_4y = 0;

        $cycle->male_dog_major_4y = 0;
        $cycle->female_dog_major_4y = 0;

        $cycle->male_dogs = 0;
        $cycle->female_dogs = 0;

        $cycle->total_of_dogs = 0;

        $cycle->male_cat = 0;
        $cycle->female_cat = 0;

        $cycle->total_of_cats = 0;
        $cycle->total = 0;
        $cycle->goal = 0;

        foreach ($cycle->supports as $support) {
            $support->male_dog_under_4m = 0;
            $support->female_dog_under_4m = 0;

            $support->male_dog_major_4m_under_1y = 0;
            $support->female_dog_major_4m_under_1y = 0;

            $support->male_dog_major_1y_under_2y = 0;
            $support->female_dog_major_1y_under_2y = 0;

            $support->male_dog_major_2y_under_4y = 0;
            $support->female_dog_major_2y_under_4y = 0;

            $support->male_dog_major_4y = 0;
            $support->female_dog_major_4y = 0;

            $support->male_dogs = 0;
            $support->female_dogs = 0;

            $support->total_of_dogs = 0;

            $support->male_cat = 0;
            $support->female_cat = 0;

            $support->total_of_cats = 0;
            $support->total = 0;
            $support->goal = 0;

            $indexSaad = null;

            foreach ($arraySaad as $saad) {
                if ($support->saads[0]->id == $saad->id) {
                    $indexSaad = $saad->id;
                    break;
                }
            }

            if ($indexSaad == null) {
                $saad = new stdClass();
                $saad->id = $support->saads[0]->id;
                $saad->name = $support->saads[0]->name;
                $indexSaad = $support->saads[0]->id;

                $arraySaad[$indexSaad] = $saad;

                $arraySaad[$indexSaad]->male_dog_under_4m = 0;
                $arraySaad[$indexSaad]->female_dog_under_4m = 0;

                $arraySaad[$indexSaad]->male_dog_major_4m_under_1y = 0;
                $arraySaad[$indexSaad]->female_dog_major_4m_under_1y = 0;

                $arraySaad[$indexSaad]->male_dog_major_1y_under_2y = 0;
                $arraySaad[$indexSaad]->female_dog_major_1y_under_2y = 0;

                $arraySaad[$indexSaad]->male_dog_major_2y_under_4y = 0;
                $arraySaad[$indexSaad]->female_dog_major_2y_under_4y = 0;

                $arraySaad[$indexSaad]->male_dog_major_4y = 0;
                $arraySaad[$indexSaad]->female_dog_major_4y = 0;

                $arraySaad[$indexSaad]->male_dogs = 0;
                $arraySaad[$indexSaad]->female_dogs = 0;

                $arraySaad[$indexSaad]->total_of_dogs = 0;

                $arraySaad[$indexSaad]->male_cat = 0;
                $arraySaad[$indexSaad]->female_cat = 0;

                $arraySaad[$indexSaad]->total_of_cats = 0;
                $arraySaad[$indexSaad]->total = 0;
                $arraySaad[$indexSaad]->goal = 0;
            }

            foreach ($support->points as $point) {
                $support->male_dog_under_4m += $point->male_dog_under_4m;
                $support->female_dog_under_4m += $point->female_dog_under_4m;

                $support->male_dog_major_4m_under_1y += $point->male_dog_major_4m_under_1y;
                $support->female_dog_major_4m_under_1y += $point->female_dog_major_4m_under_1y;

                $support->male_dog_major_1y_under_2y += $point->male_dog_major_1y_under_2y;
                $support->female_dog_major_1y_under_2y += $point->female_dog_major_1y_under_2y;

                $support->male_dog_major_2y_under_4y += $point->male_dog_major_2y_under_4y;
                $support->female_dog_major_2y_under_4y += $point->female_dog_major_2y_under_4y;

                $support->male_dog_major_4y += $point->male_dog_major_4y;
                $support->female_dog_major_4y += $point->female_dog_major_4y;

                $support->male_dogs += $point->male_dogs;
                $support->female_dogs += $point->female_dogs;

                $support->total_of_dogs += $point->total_of_dogs;

                $support->male_cat += $point->male_cat;
                $support->female_cat += $point->female_cat;

                $support->total_of_cats += $point->total_of_cats;
                $support->total += $point->total;
                $support->goal += $point->goal;
            }

            $arraySaad[$indexSaad]->male_dog_under_4m += $support->male_dog_under_4m;
            $arraySaad[$indexSaad]->female_dog_under_4m += $support->female_dog_under_4m;

            $arraySaad[$indexSaad]->male_dog_major_4m_under_1y += $support->male_dog_major_4m_under_1y;
            $arraySaad[$indexSaad]->female_dog_major_4m_under_1y += $support->female_dog_major_4m_under_1y;

            $arraySaad[$indexSaad]->male_dog_major_1y_under_2y += $support->male_dog_major_1y_under_2y;
            $arraySaad[$indexSaad]->female_dog_major_1y_under_2y += $support->female_dog_major_1y_under_2y;

            $arraySaad[$indexSaad]->male_dog_major_2y_under_4y += $support->male_dog_major_2y_under_4y;
            $arraySaad[$indexSaad]->female_dog_major_2y_under_4y += $support->female_dog_major_2y_under_4y;

            $arraySaad[$indexSaad]->male_dog_major_4y += $support->male_dog_major_4y;
            $arraySaad[$indexSaad]->female_dog_major_4y += $support->female_dog_major_4y;

            $arraySaad[$indexSaad]->male_dogs += $support->male_dogs;
            $arraySaad[$indexSaad]->female_dogs += $support->female_dogs;

            $arraySaad[$indexSaad]->total_of_dogs += $support->total_of_dogs;

            $arraySaad[$indexSaad]->male_cat += $support->male_cat;
            $arraySaad[$indexSaad]->female_cat += $support->female_cat;

            $arraySaad[$indexSaad]->total_of_cats += $support->total_of_cats;
            $arraySaad[$indexSaad]->total += $support->total;
            $arraySaad[$indexSaad]->goal += $support->goal;

            $cycle->male_dog_under_4m += $support->male_dog_under_4m;
            $cycle->female_dog_under_4m += $support->female_dog_under_4m;

            $cycle->male_dog_major_4m_under_1y += $support->male_dog_major_4m_under_1y;
            $cycle->female_dog_major_4m_under_1y += $support->female_dog_major_4m_under_1y;

            $cycle->male_dog_major_1y_under_2y += $support->male_dog_major_1y_under_2y;
            $cycle->female_dog_major_1y_under_2y += $support->female_dog_major_1y_under_2y;

            $cycle->male_dog_major_2y_under_4y += $support->male_dog_major_2y_under_4y;
            $cycle->female_dog_major_2y_under_4y += $support->female_dog_major_2y_under_4y;

            $cycle->male_dog_major_4y += $support->male_dog_major_4y;
            $cycle->female_dog_major_4y += $support->female_dog_major_4y;

            $cycle->male_dogs += $support->male_dogs;
            $cycle->female_dogs += $support->female_dogs;

            $cycle->total_of_dogs += $support->total_of_dogs;

            $cycle->male_cat += $support->male_cat;
            $cycle->female_cat += $support->female_cat;

            $cycle->total_of_cats += $support->total_of_cats;
            $cycle->total += $support->total;
            $cycle->goal += $support->goal;
        }

        $cycle->saads = $arraySaad;

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
            'statisticCoordinator',
            'statistics',
            'transports',
            'zoonoses'

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
}
