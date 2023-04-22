<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\RubberShop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RubberShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = RubberShop::select('*')->selectRaw(
            'ST_AsGeoJSON(geometry) AS geometry'
        )->get();
        return $list;
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
     * @param  \App\Models\RubberShop  $rubberShop
     * @return \Illuminate\Http\Response
     */
    public function show(RubberShop $rubberShop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RubberShop  $rubberShop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RubberShop $rubberShop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RubberShop  $rubberShop
     * @return \Illuminate\Http\Response
     */
    public function destroy(RubberShop $rubberShop)
    {
        //
    }
}
