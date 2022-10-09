<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\CampaignCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;

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
            'campaign_id' => $request->campaign_id
        ]);

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

    public function report(Request $request, $id)
    {
        $today = date("m-d-Y");
        $cycle = CampaignCycle::with([
            'supports.coordinator',
            'supports.support.neighborhoodAlias.neighborhood',
            'supports.supervisors',
            'supports.drivers',
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
}
