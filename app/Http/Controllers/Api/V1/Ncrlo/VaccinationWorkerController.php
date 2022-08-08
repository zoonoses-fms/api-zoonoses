<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VaccinationWorker;

class VaccinationWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('list_type')) {
            $campaign_cycle_id = $request->campaign_cycle_id;
            if ($request->list_type == 'all') {
                return VaccinationWorker::when(
                    $request->has('keyword'),
                    function ($query) use ($request) {
                        $keyword = $request->keyword;
                        return $query->whereRaw(
                            "unaccent(name) ilike unaccent('%{$keyword}%')"
                        )->orWhereRaw(
                            "unaccent(registration) ilike unaccent('%{$keyword}%')"
                        );
                    }
                )
                ->orderBy('name', 'asc')
                ->get();
            } elseif ($request->list_type == 'free') {
                $listNotFreeWorkers = VaccinationWorker::listNotFreeWorkers($campaign_cycle_id);
                return VaccinationWorker::listFreeWorkers($request, $listNotFreeWorkers);
            } elseif ($request->list_type == 'coordinator') {
                $coordinator_id = $request->coordinator_id;
                $listNotFreeWorkers = VaccinationWorker::listNotFreeWorkers($campaign_cycle_id);
                $index = array_search($coordinator_id, $listNotFreeWorkers);
                unset($listNotFreeWorkers[$index]);
                return VaccinationWorker::listFreeWorkers($request, $listNotFreeWorkers);
            } elseif (
                $request->list_type == 'supervisors' ||
                $request->list_type == 'drivers' ||
                $request->list_type == 'vaccinators' ||
                $request->list_type == 'assistants'
            ) {
                $ids = $request->ids;
                $listNotFreeWorkers = VaccinationWorker::listNotFreeWorkers($campaign_cycle_id);
                foreach ($ids as $id) {
                    $index = array_search($id, $listNotFreeWorkers);
                    unset($listNotFreeWorkers[$index]);
                }
                return VaccinationWorker::listFreeWorkers($request, $listNotFreeWorkers);
            }
        }
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        } else {
            $perPage = 5;
        }

        $workers = VaccinationWorker::when(
            $request->has('keyword'),
            function ($query) use ($request) {
                $keyword = $request->keyword;
                return $query->whereRaw(
                    "unaccent(name) ilike unaccent('%{$keyword}%')"
                )->orWhereRaw(
                    "unaccent(registration) ilike unaccent('%{$keyword}%')"
                );
            }
        )->orderBy('updated_at', 'desc')->paginate($perPage);

        return $workers;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $worker = VaccinationWorker::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'cpf' => $request->cpf,
            'registration' => $request->registration
        ]);
        return $worker;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $worker = VaccinationWorker::find($id);
        return $worker;
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
        $worker = VaccinationWorker::find($id);
        $worker->name = $request->name;
        $worker->cpf = $request->cpf;
        $worker->phone = $request->phone;
        $worker->registration = $request->registration;

        $worker->save();
        return $worker;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $worker = VaccinationWorker::find($id);
        $worker->delete();
    }
}
