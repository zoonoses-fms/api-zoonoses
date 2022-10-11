<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VaccinationWorker;
use App\Models\CampaignCycle;
use stdClass;

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
                $request->list_type == 'assistants' ||
                $request->list_type == 'annotators' ||
                // $request->list_type == 'rural_supervisors' ||
                $request->list_type == 'rural_assistants'
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

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        } else {
            $perPage = 5;
        }

        $cycle_id = $request->cycle_id;

        $cycle = CampaignCycle::find($cycle_id);

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
        )->orderBy('updated_at', 'desc')->get();

        $allocations = [];

        foreach ($workers as $worker) {
            $coordinatorSupports = $cycle->supports->where('coordinator_id', $worker->id);
            if (count($coordinatorSupports) > 0) {
                foreach ($coordinatorSupports as $support) {
                    $support->load('support');
                }
                $worker->coordinations = $coordinatorSupports;
                $coordinatorWorker = new stdClass();
                $coordinatorWorker->name = $worker->name;
                $coordinatorWorker->occupation = 'Coordenador - PA';
                $coordinatorWorker->location = $support->support->name;
                $allocations[] = $coordinatorWorker;
            }
            $supports = $cycle->supports;

            foreach ($supports as $support) {
                $supervisorsSupports = $support->supervisors()->wherePivot('supervisor_id', $worker->id)->get();
                $driversSupports = $support->drivers()->wherePivot('driver_id', $worker->id)->get();
                $assistantsSupports = $support->assistants()->wherePivot('assistant_id', $worker->id)->get();
                $vaccinatorsSupports = $support->vaccinators()->wherePivot('vaccinator_id', $worker->id)->get();


                if ($supervisorsSupports->count() > 0) {
                    $support->load('support');
                    $worker->supervisors = $support;
                    $supervisorWorker = new stdClass();
                    $supervisorWorker->name = $worker->name;
                    $supervisorWorker->occupation = 'Supervisor - PA';
                    $supervisorWorker->location = $support->support->name;
                    $allocations[] = $supervisorWorker;
                }

                if ($driversSupports->count() > 0) {
                    $support->load('support');
                    $worker->drivers = $support;
                    $driverWorker = new stdClass();
                    $driverWorker->name = $worker->name;
                    $driverWorker->occupation = 'Motorista - PA';
                    $driverWorker->location = $support->support->name;
                    $allocations[] = $driverWorker;
                }

                if ($assistantsSupports->count() > 0) {
                    $support->load('support');
                    $worker->assistants = $support;
                    $assistantWorker = new stdClass();
                    $assistantWorker->name = $worker->name;
                    $assistantWorker->occupation = 'Auxiliares - PA';
                    $assistantWorker->location = $support->support->name;
                    $allocations[] = $assistantWorker;
                }

                if ($vaccinatorsSupports->count() > 0) {
                    $support->load('support');
                    $worker->vaccinators = $support;
                    $vaccinatorWorker = new stdClass();
                    $vaccinatorWorker->name = $worker->name;
                    $vaccinatorWorker->occupation = 'Vacinador Reserva - PA';
                    $vaccinatorWorker->location = $support->support->name;
                    $allocations[] = $vaccinatorWorker;
                }

                foreach ($support->points as $point) {
                    $vaccinatorsPoints = $point->vaccinators()->wherePivot('vaccinator_id', $worker->id)->get();
                    $annotatorsPoints = $point->annotators()->wherePivot('annotator_id', $worker->id)->get();

                    if ($vaccinatorsPoints->count() > 0) {
                        $point->load('point');
                        $worker->vaccinators = $point;
                        $vaccinatorWorker = new stdClass();
                        $vaccinatorWorker->name = $worker->name;
                        $vaccinatorWorker->occupation = 'Vacinador';
                        $vaccinatorWorker->location = $point->point->name;
                        $allocations[] = $vaccinatorWorker;
                    }

                    if ($annotatorsPoints->count() > 0) {
                        $point->load('point');
                        $worker->annotators = $point;
                        $annotatorWorker = new stdClass();
                        $annotatorWorker->name = $worker->name;
                        $annotatorWorker->occupation = 'Anotador';
                        $annotatorWorker->location = $point->point->name;
                        $allocations[] = $annotatorWorker;
                    }
                }
            }
        }

        return $allocations;
    }
}
