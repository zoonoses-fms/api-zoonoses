<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->user()->tokenCan('zoonoses:admin')) {
            return response()->json(['error' => 'Not authorized.'], 401);
        }

        if ($request->has('per_page')) {
            $perPage = $request->input('per_page');
        } else {
            $perPage = 5;
        }

        if ($request->has('search') && count($request->search) > 0) {
            $search = $request->search;
            $teams = Team::with(['core'])->where('name', 'ilike', '%' . $search . '%')->paginate($perPage);
        } else {
            $teams = Team::with(['core'])->paginate($perPage);
        }

        return $teams;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->user()->tokenCan('zoonoses:admin')) {
            return response()->json(['error' => 'Not authorized.'], 401);
        }

        $request->validate([
            'number' => 'required',
            'core_id' => 'required',
            'user_id' => 'required'
        ]);

        $team = Team::create($request->all());

        return $team;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
