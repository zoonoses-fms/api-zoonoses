<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\CampaingSupport;
use Illuminate\Http\Request;

class CampaingSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('campaing_support_id')) {
            $support = CampaingSupport::find($request->campaing_support_id);
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
        $support = new CampaingSupport();
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
        $support = CampaingSupport::with(
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
        $support = CampaingSupport::findOrFail($id);
        $support->goal = $request->goal;
        $support->mileage = $request->mileage;
        $support->coordinator_id = $request->coordinator_id;
        $support->supervisors()->sync($request->supervisors);
        $support->drivers()->sync($request->drivers);
        $support->assistants()->sync($request->assistants);
        $support->vaccinators()->sync($request->vaccinators);
        $support->saads()->sync($request->saads);
        $support->is_rural = $request->is_rural;
        $support->ruralSupervisors()->sync($request->rural_supervisors);
        $support->ruralAssistants()->sync($request->rural_assistants);

        $support->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $support = CampaingSupport::with([
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
}
