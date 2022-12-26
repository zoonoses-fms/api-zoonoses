<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use App\Models\ProfileWorker;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class ProfileWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('campaign_id')) {
            $campaign = Campaign::find($request->campaign_id);
            $profileIds = [];
            foreach ($campaign->profilesAll as $profile) {
                $profileIds[] = $profile->id;
            }
            $profiles = ProfileWorker::whereNotIn('id', $profileIds)->get();

            return $profiles;
        }
        $profiles = ProfileWorker::get();

        return $profiles;
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
            'name' => 'required',
            'scope' => 'required'
        ]);

        $profile = ProfileWorker::create([
            'name' => $request->name,
            'scope' => $request->scope,
            'management' => $request->management,
            'is_single_allocation' => $request->has('is_single_allocation') ? $request->is_single_allocation : 'true',
            'is_pre_campaign' => $request->has('is_pre_campaign') ? $request->is_pre_campaign : 'false',
            'is_multiple' => $request->has('is_multiple') ? $request->is_multiple : 'false',
            'is_rural' => $request->has('is_rural') ? $request->is_rural : 'false'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProfileWorker  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(ProfileWorker $profile)
    {
        return $profile;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProfileWorker  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $profile = ProfileWorker::findOrFail($id);

        $profile->name = $request->name;
        $profile->scope = $request->scope;
        $profile->management = $request->management;
        $profile->is_single_allocation = $request->is_single_allocation;
        $profile->is_pre_campaign = $request->is_pre_campaign;
        $profile->is_multiple = $request->is_multiple;
        $profile->is_rural = $request->is_rural;

        $profile->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProfileWorker  $ProfileWorker
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfileWorker $profile)
    {
        $profile->delete();
    }
}
