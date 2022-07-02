<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\VaccinationPoint;
use App\Models\VaccinationSupport;
use Illuminate\Http\Request;

class VaccinationSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vaccinationSupports = VaccinationSupport::get();
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VaccinationSupportPoint  $vaccinationSupportPoint
     * @return \Illuminate\Http\Response
     */
    public function show(VaccinationSupportPoint $vaccinationSupportPoint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VaccinationSupportPoint  $vaccinationSupportPoint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VaccinationSupportPoint $vaccinationSupportPoint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VaccinationSupportPoint  $vaccinationSupportPoint
     * @return \Illuminate\Http\Response
     */
    public function destroy(VaccinationSupportPoint $vaccinationSupportPoint)
    {
        //
    }
}
