<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\VaccinationPoint;
use App\Models\VaccinationSupport;
use App\Models\CampaignCycle;
use Illuminate\Http\Request;
use App\Models\Location\The\TheNeighborhoodAlias;

class VaccinationSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('campaign_cycle_id')) {
            $cycle = CampaignCycle::with('supports.support')
                ->findOrFail($request->get('campaign_cycle_id'));
            $listSupport = [];

            foreach ($cycle->supports as $supportPoint) {
                $listSupport[] = $supportPoint->support->id;
            }

            $vaccinationSupports = VaccinationSupport::whereNotIn('id', $listSupport)->get();

            foreach ($vaccinationSupports as $vaccinationSupport) {
                $vaccinationSupport->neighborhood = $vaccinationSupport->getNeighborhood();
            }

            return $vaccinationSupports;
        }

        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        } else {
            $perPage = 5;
        }

        $vaccinationSupports = VaccinationSupport::select(
            'id',
            'name',
            'address',
            'number',
            'address_complement',
            'the_neighborhood_alias_id'
        )
        ->selectRaw(
            'ST_AsGeoJSON(geometry) AS geometry'
        )
        ->when(
            $request->has('keyword'),
            function ($query) use ($request) {
                $keyword = $request->keyword;
                return $query->whereRaw(
                    "unaccent(name) ilike unaccent('%{$keyword}%')"
                )->orWhereRaw(
                    "unaccent(address) ilike unaccent('%{$keyword}%')"
                );
            }
        )->orderBy('updated_at', 'desc')->paginate($perPage);

        foreach ($vaccinationSupports->items() as $vaccinationSupport) {
            $vaccinationSupport->neighborhood = $vaccinationSupport->getNeighborhood();
        }

        return $vaccinationSupports;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vaccinationSupport = new VaccinationSupport();
        $vaccinationSupport->name = $request->name;
        $vaccinationSupport->address = $request->address;
        $vaccinationSupport->number = $request->number;
        $vaccinationSupport->address_complement = $request->address_complement;

        if (strlen($request->neighborhood) >= 3) {
            $neighborhoodAlias =
            TheNeighborhoodAlias::getOrCreate($request->neighborhood);

            $vaccinationSupport
            ->the_neighborhood_alias_id = $neighborhoodAlias->id;
        }
        if ($request->geometry != null) {
            $geometry = json_encode($request->geometry);

            $vaccinationSupport->geometry = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
        }

        $vaccinationSupport->save();
        return $vaccinationSupport;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VaccinationSupport  $VaccinationSupport
     * @return \Illuminate\Http\Response
     */
    public function show(VaccinationSupport $VaccinationSupport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $vaccinationSupport = VaccinationSupport::find($id);
        $vaccinationSupport->name = $request->name;
        $vaccinationSupport->address = $request->address;
        $vaccinationSupport->number = $request->number;
        $vaccinationSupport->address_complement = $request->address_complement;

        if (strlen($request->neighborhood) >= 3) {
            $neighborhoodAlias =
            TheNeighborhoodAlias::getOrCreate($request->neighborhood);

            $vaccinationSupport
            ->the_neighborhood_alias_id = $neighborhoodAlias->id;
        }
        $geometry = json_encode($request->geometry);

        $vaccinationSupport->geometry = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");

        $vaccinationSupport->save();
        return $vaccinationSupport;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vaccinationSupport = VaccinationSupport::find($id);
        $vaccinationSupport->delete();
    }
}
