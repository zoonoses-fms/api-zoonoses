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
            'campaing_id' => 'required'
        ]);

        $cycle = CampaignCycle::create([
            'number' => $request->number,
            'description' => $request->description,
            'start' => $request->start,
            'end' => $request->end,
            'campaing_id' => $request->campaing_id,
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
            'campaing',
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

        $total['cycle']['total'] = 0;
        $total['before']['total'] = 0;
        $total['before']['cold_chain_coordinator'] = 0;
        $total['before']['cold_chain_nurse'] = 0;
        $total['before']['cold_chain'] = 0;
        $total['before']['driver_cold_chain'] = 0;
        $total['before']['transport'] = 0;
        $total['before']['zoonose'] = 0;

        $count['cold_chain_coordinator'] = 0;
        $count['cold_chain_nurse'] = 0;
        $count['cold_chain'] = 0;
        $count['driver_cold_chain'] = 0;
        $count['transport'] = 0;
        $count['zoonose'] = 0;
        $count['statistic_coordinator'] = 0;
        $count['statistic'] = 0;
        $count['vaccinator'] = 0;
        $count['annotator'] = 0;
        $count['rural_supervisor'] = 0;
        $count['rural_assistant'] = 0;
        $count['coordinator'] = 0;
        $count['supervisor'] = 0;
        $count['assistant'] = 0;
        $count['driver'] = 0;

        if (!is_null($cycle->coldChainCoordinator)) {
            $total['before']['cold_chain_coordinator'] += $cycle->campaing->cold_chain_coordinator_cost;
            $total['before']['total'] += $total['before']['cold_chain_coordinator'];
            $count['cold_chain_coordinator'] += 1;
        }

        if (!is_null($cycle->coldChainNurse)) {
            $total['before']['cold_chain_nurse'] += $cycle->campaing->cold_chain_nurse_cost;
            $total['before']['total'] += $total['before']['cold_chain_nurse'];
            $count['cold_chain_nurse'] += 1;
        }

        $total['before']['cold_chain'] += ($cycle->campaing->cold_chain_cost * count($cycle->beforeColdChains));
        $total['before']['total' ] += $total['before']['cold_chain'];
        $count['cold_chain'] += count($cycle->beforeColdChains);

        $total['before']['driver_cold_chain'] += ($cycle->campaing->driver_cost * count($cycle->beforeDriverColdChains));
        $total['before']['total'] += $total['before']['driver_cold_chain'];
        $count['driver_cold_chain'] += count($cycle->beforeDriverColdChains);

        $total['before']['transport'] += ($cycle->campaing->transport_cost * count($cycle->beforeTransports));
        $total['before']['total'] += $total['before']['transport'];
        $count['transport'] += count($cycle->beforeTransports);

        $total['before']['zoonose'] += ($cycle->campaing->zoonoses_cost * count($cycle->beforeZoonoses));
        $total['before']['total'] += $total['before']['zoonose'];
        $count[ 'zoonose'] += count($cycle->beforeZoonoses);

        $total['cycle']['total'] += $total['before']['total'];

        $total['start']['total'] = 0;
        $total['start']['cold_chain_coordinator'] = 0;
        $total['start']['cold_chain_nurse'] = 0;
        $total['start']['cold_chain'] = 0;
        $total['start']['driver_cold_chain'] = 0;
        $total['start']['transport'] = 0;
        $total['start']['zoonose'] = 0;
        $total['start']['statistic_coordinator'] = 0;
        $total['start']['statistic'] = 0;
        $total['start']['vaccinator'] = 0;
        $total['start']['annotator'] = 0;
        $total['start']['rural_supervisor'] = 0;
        $total['start']['rural_assistant'] = 0;
        $total['start']['coordinator'] = 0;
        $total['start']['supervisor'] = 0;
        $total['start']['assistant'] = 0;
        $total['start']['driver'] = 0;

        if (!is_null($cycle->coldChainCoordinator)) {
            $total['start']['cold_chain_coordinator'] += $cycle->campaing->cold_chain_coordinator_cost;
            $total['start']['total'] += $total['start']['cold_chain_coordinator'];
            $count['cold_chain_coordinator'] += 1;
        }

        if (!is_null($cycle->coldChainNurse)) {
            $total['start']['cold_chain_nurse'] += $cycle->campaing->cold_chain_nurse_cost;
            $total['start']['total'] += $total['start']['cold_chain_nurse'];
            $count['cold_chain_nurse'] += 1;
        }

        $total['start']['cold_chain'] += ($cycle->campaing->cold_chain_cost * count($cycle->startColdChains));
        $total['start']['total'] += $total['start']['cold_chain'];
        $count['cold_chain'] += count($cycle->startColdChains);

        $total['start']['driver_cold_chain'] += ($cycle->campaing->driver_cost * count($cycle->startDriverColdChains));
        $total['start']['total'] += $total['start']['driver_cold_chain'];
        $count['driver_cold_chain'] += count($cycle->startDriverColdChains);

        $total['start']['transport'] += ($cycle->campaing->transport_cost * count($cycle->startTransports));
        $total['start']['total'] += $total['start']['transport'];
        $count['transport'] += count($cycle->startTransports);

        $total['start']['zoonose'] += ($cycle->campaing->zoonoses_cost * count($cycle->startZoonoses));
        $total['start']['total'] += $total['start']['zoonose'];
        $count['zoonose'] += count($cycle->startZoonoses);

        $total['start']['statistic_coordinator'] += $cycle->campaing->statistic_coordinator_cost;
        $total['start']['total'] += $total['start']['statistic_coordinator'];
        $count['statistic_coordinator'] += 1;

        $total['start']['statistic'] += ($cycle->campaing->statisic_cost * count($cycle->statistics));
        $total['start']['total'] += $total['start']['statistic'];
        $count['statistic'] += count($cycle->statistics);

        $registrationRuralSupervisors = [];
        $registrationRuralAssistants = [];

        foreach ($cycle->supports as $support) {
            $total['start']['vaccinator'] += ($cycle->campaing->vaccinator_cost * count($support->vaccinators));

            if ($support->is_rural) {
                for ($i = 0; $i < count($support->ruralSupervisors); $i++) {
                    if (!in_array($support->ruralSupervisors[$i]->registration, $registrationRuralSupervisors)) {
                        $total['start']['rural_supervisor'] += $cycle->campaing->rural_supervisor_cost;
                        $registrationRuralSupervisors[] = $support->ruralSupervisors[$i]->registration;
                        $count['rural_supervisor'] += 1;
                    } else {
                        unset($support->ruralSupervisors[$i]);
                    }
                }

                for ($i = 0; $i < count($support->ruralAssistants); $i++) {
                    if (!in_array($support->ruralAssistants[$i]->registration, $registrationRuralAssistants)) {
                        $total['start']['rural_assistant'] += $cycle->campaing->rural_assistant_cost;
                        $registrationRuralAssistants[] = $support->ruralAssistants[$i]->registration;
                        $count['rural_assistant'] += 1;
                    } else {
                        unset($support->ruralAssistants[$i]);
                    }
                }
            } else {
                if (!is_null($support->coordinator)) {
                    $total['start']['coordinator'] += $cycle->campaing->coordinator_cost;
                    $count['coordinator'] += 1;
                }

                $total['start']['supervisor'] += ($cycle->campaing->supervisor_cost * count($support->supervisors));
                $total['start']['assistant'] += ($cycle->campaing->assistant_cost * count($support->assistants));
                $total['start']['driver'] += ($cycle->campaing->driver_cost * count($support->drivers));
                $total['start']['vaccinator'] += ($cycle->campaing->vaccinator_cost * count($support->vaccinators));

                $count['supervisor'] += count($support->supervisors);
                $count['assistant'] += count($support->assistants);
                $count['driver'] += count($support->drivers);
                $count['vaccinator'] += count($support->vaccinators);
            }


            foreach ($support->points as $point) {
                $total['start']['vaccinator'] += ($cycle->campaing->vaccinator_cost * count($point->vaccinators));
                $total['start']['annotator'] += ($cycle->campaing->annotators_cost * count($point->annotators));

                $count['vaccinator'] += count($point->vaccinators);
                $count['annotator'] += count($point->annotators);
            }
        }
        $total['start']['total'] += $total['start']['rural_supervisor'];
        $total['start']['total'] += $total['start']['rural_assistant'];

        $total['start']['total'] += $total['start']['coordinator'];
        $total['start']['total'] += $total['start']['supervisor'];
        $total['start']['total'] += $total['start']['assistant'];
        $total['start']['total'] += $total['start']['driver'];

        $total['start']['total'] += $total['start']['vaccinator'];
        $total['start']['total'] += $total['start']['annotator'];

        $total['cycle']['cold_chain_coordinator']
            = $total['before']['cold_chain_coordinator'] + $total['start']['cold_chain_coordinator'];

        $total['cycle']['cold_chain_nurse']
            = $total['before']['cold_chain_nurse'] + $total['start']['cold_chain_nurse'];

        $total['cycle']['cold_chain']
            = $total['before']['cold_chain'] + $total['start']['cold_chain'];

        $total['cycle']['driver_cold_chain']
            = $total['before']['driver_cold_chain'] + $total['start']['driver_cold_chain'];

        $total['cycle']['transport']
            = $total['before']['transport'] + $total['start']['transport'];

        $total['cycle']['zoonose']
            = $total['before']['zoonose'] + $total['start']['zoonose'];

        $total['cycle']['statistic_coordinator'] = $total['start']['statistic_coordinator'];

        $total['cycle']['statistic'] = $total['start']['statistic'];

        $total['cycle']['vaccinator'] = $total['start']['vaccinator'];
        $total['cycle']['annotator'] = $total['start']['annotator'];
        $total['cycle']['rural_supervisor'] =  $total['start']['rural_supervisor'];
        $total['cycle']['rural_assistant'] = $total['start']['rural_assistant'];
        $total['cycle']['coordinator'] = $total['start']['coordinator'];
        $total['cycle']['supervisor'] = $total['start']['supervisor'];
        $total['cycle']['assistant'] = $total['start']['assistant'];
        $total['cycle']['driver'] = $total['start']['driver'];

        $total['cycle']['total'] += $total['start']['total'];

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
