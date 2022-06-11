<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\HealthUnit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HealthUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $healthUnits = HealthUnit::whereRaw(
                "left(cnes_code::text, length('{$keyword}')) ilike unaccent('%{$keyword}%')"
            )->orWhereRaw(
                "unaccent(alias_company_name) ilike unaccent('%{$keyword}%')"
            )->get();

            foreach ($healthUnits as $healthUnit) {
                $healthUnit->city = $healthUnit->city();
            }
            return $healthUnits;
        } elseif ($request->has('cnes_code')) {
            $cnes_code = $request->get('cnes_code');
            $healthUnit = HealthUnit::where(
                'cnes_code',
                $cnes_code
            )->first();

            $healthUnit->city = $healthUnit->city();

            return $healthUnit;
        } else {

            if ($request->has('per_page')) {
                $perPage = $request->input('per_page');
            } else {
                $perPage = 5;
            }

            $healthUnits = HealthUnit::orderBy('latitude')->paginate($perPage);

            foreach ($healthUnits as $healthUnit) {
                $healthUnit->state = $healthUnit->state()->name;
                $healthUnit->city = $healthUnit->city()->name;
            }

            return $healthUnits;
        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $healthUnit = HealthUnit::where('cnes_code', $id)->first();

        return $healthUnit;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function partialUpdate(Request $request, $id = null)
    {
        if ($request->has('action')) {
            $action = $request->get('action');
            if ($action == 'geo_reference') {
                try {
                    $healthUnits = [];
                    foreach ($request->ids as $id) {
                        $healthUnit = HealthUnit::find($id);
                        $healthUnits[] = $healthUnit->geocodeAddressFull();
                    }
                    return response()->json(
                        [
                            'status' => 'Success',
                            'message' => 'Update GEOCode',
                            'data' => $healthUnits,
                            'code' => 201,
                        ],
                        200
                    )->header('Content-Type', 'text/plain');
                } catch (\Throwable $th) {
                    return response()->json(
                        [
                            'status' => 'Error',
                            'message' => 'Update GEOCode',
                            'data' => $healthUnits[count($healthUnits) - 1],
                            'code' => 503,
                        ],
                        503
                    )->header('Content-Type', 'text/plain');
                }
            }
        }

        return null;
    }
}
