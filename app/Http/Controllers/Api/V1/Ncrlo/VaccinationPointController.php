<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\VaccinationPoint;
use App\Models\CampaingSupport;
use Illuminate\Http\Request;
use App\Models\Location\The\TheNeighborhoodAlias;

class VaccinationPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('support_id')) {
            $campaingSupport = CampaingSupport::with('cycle.supports.points.point')
                ->findOrFail($request->get('support_id'));
            $listPoints = [];

            foreach ($campaingSupport->cycle->supports as $supportPoint) {
                foreach ($supportPoint->points as $vaccinationPoint) {
                    $listPoints[] = $vaccinationPoint->point->id;
                }
            }
            $vaccinationPoints = VaccinationPoint::whereNotIn('id', $listPoints)->get();

            foreach ($vaccinationPoints as $vaccinationPoint) {
                $vaccinationPoint->neighborhood = $vaccinationPoint->getNeighborhood();
            }

            return $vaccinationPoints;
        }

        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        } else {
            $perPage = 5;
        }

        $vaccinationPoints = VaccinationPoint::select(
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

        foreach ($vaccinationPoints as $vaccinationPoint) {
            $vaccinationPoint->neighborhood = $vaccinationPoint->getNeighborhood();
        }

        return $vaccinationPoints;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vaccinationPoint = new VaccinationPoint();
        $vaccinationPoint->name = $request->name;
        $vaccinationPoint->address = $request->address;
        $vaccinationPoint->number = $request->number;
        $vaccinationPoint->address_complement = $request->address_complement;

        if (strlen($request->neighborhood) >= 3) {
            $neighborhoodAlias =
            TheNeighborhoodAlias::getOrCreate($request->neighborhood);

            $vaccinationPoint
            ->the_neighborhood_alias_id = $neighborhoodAlias->id;
        }

        if ($request->geometry != null) {
            $geometry = json_encode($request->geometry);

            $vaccinationPoint->geometry = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
        }

        $vaccinationPoint->save();
        return $vaccinationPoint;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VaccinationPoint  $vaccinationPoint
     * @return \Illuminate\Http\Response
     */
    public function show(VaccinationPoint $vaccinationPoint)
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
        $vaccinationPoint = VaccinationPoint::find($id);
        $vaccinationPoint->name = $request->name;
        $vaccinationPoint->address = $request->address;
        $vaccinationPoint->number = $request->number;
        $vaccinationPoint->address_complement = $request->address_complement;

        if (strlen($request->neighborhood) >= 3) {
            $neighborhoodAlias =
            TheNeighborhoodAlias::getOrCreate($request->neighborhood);

            $vaccinationPoint
            ->the_neighborhood_alias_id = $neighborhoodAlias->id;
        }

        if ($request->geometry != null) {
            $geometry = json_encode($request->geometry);

            $vaccinationPoint->geometry = DB::raw("ST_SetSRID(ST_GeomFromGeoJSON('{$geometry}'), 3857)");
        }
        $vaccinationPoint->save();
        return $neighborhoodAlias;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vaccinationPoint = VaccinationPoint::find($id);
        $vaccinationPoint->delete();
    }
}
