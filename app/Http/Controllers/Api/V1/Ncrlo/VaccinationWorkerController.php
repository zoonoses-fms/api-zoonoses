<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VaccinationWorker;
use App\Models\CampaignCycle;
use App\Models\Plataform;
use Illuminate\Validation\ValidationException;
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
        $listType = array(
            'supervisors',
            'drivers',
            'vaccinators',
            'assistants',
            'annotators',
            'rural_supervisors',
            'rural_assistants',
            'statistics',
            'transports',
            'before_cold_chains',
            'start_cold_chains',
            'driver_cold_chains',
            'zoonoses'
        );

        $listTypeCoordinator = array(
            'coordinator',
            'statistic_coordinator',
            'cold_chain_coordinator',
            'cold_chain_nurse'
        );

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
            } elseif (in_array($request->list_type, $listTypeCoordinator)) {
                $coordinator_id = $request->coordinator_id;
                $listNotFreeWorkers = VaccinationWorker::listNotFreeWorkers($campaign_cycle_id);
                $index = array_search($coordinator_id, $listNotFreeWorkers);
                unset($listNotFreeWorkers[$index]);
                return VaccinationWorker::listFreeWorkers($request, $listNotFreeWorkers);
            } elseif (in_array($request->list_type, $listType)) {
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'registration' => 'required',
                'plataforma_uuid' => 'required',
                'plataforma_password' => 'required'
            ]);

            $plataform = Plataform::where(
                'uuid',
                '=',
                $request->plataforma_uuid
            )->where(
                'password',
                '=',
                $request->plataforma_password
            )->first();

            if (!$plataform) {
                throw ValidationException::withMessages([
                    'plataform' => ['Credenciais incorretas'],
                ]);
            }

            $worker = VaccinationWorker::where('registration', $request->registration)->firstOrFail();

            if (!$worker) {
                throw ValidationException::withMessages([
                    'worker' => ['Credenciais incorretas'],
                ]);
            }

            return $this->success($worker);
        } catch (ValidationException $e) {
            $erros = $e->errors();
            return $this->error('Login failed', 501, $erros);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchLocations(Request $request)
    {
        try {
            $request->validate([
                'registration' => 'required',
                'cycle_id' => 'required'
            ]);

            $cycle_id = $request->cycle_id;

            $cycle = CampaignCycle::find($cycle_id);

            $workers = VaccinationWorker::when(
                $request->has('registration'),
                function ($query) use ($request) {
                    $registration = $request->registration;
                    return $query->where('registration', $registration);
                }
            )->orderBy('updated_at', 'desc')->get();

            $allocations = new stdClass();
            $allocations->coordinations = [];
            $allocations->supervisors = [];
            $allocations->pointVaccinators = [];
            $allocations->pointAnnotators = [];
            $allocations->ruralSupervisors = [];
            $allocations->ruralAssistants = [];
            $allocations->ruralVaccinators = [];

            $workerIds = [];

            foreach ($workers as $worker) {
                $workerIds[] = $worker->id;
            }

            $coordinations = $cycle->supports->whereIn('coordinator_id', $workerIds);

            if (count($coordinations) > 0) {
                foreach ($coordinations as $coordinator) {
                    $coordinator->load('support');
                    $coordinator->load('points.point');
                }
                $allocations->coordinations = array_merge(
                    $allocations->coordinations,
                    $coordinations->toArray()
                );
            }

            foreach ($cycle->supports as $support) {
                if ($support->is_rural) {
                    $ruralSupervisors = $support
                        ->ruralSupervisors()
                        ->wherePivotIn('rural_supervisor_id', $workerIds)
                        ->get();

                    if (count($ruralSupervisors) > 0) {
                        $support->load('points.point');

                        $allocations->ruralSupervisors[] = $support;
                    }

                    $ruralAssistants = $support
                        ->ruralAssistants()
                        ->wherePivotIn('rural_assistant_id', $workerIds)
                        ->get();

                    if (count($ruralAssistants) > 0) {
                        $support->load('points.point');

                        $allocations->ruralAssistants[] = $support;
                    }

                    $ruralVaccinators = $support
                        ->vaccinators()
                        ->wherePivot('vaccinator_id', $worker->id)
                        ->get();

                    if (count($ruralVaccinators) > 0) {
                        $support->load('points.point');

                        $allocations->ruralVaccinators[] = $support;
                    }
                } else {
                    $supervisors = $support->points()->whereIn('supervisor_id', $workerIds)->get();
                    if (count($supervisors) > 0) {
                        foreach ($supervisors as $supervisor) {
                            $supervisor->load('point');
                        }
                        $allocations->supervisors = array_merge(
                            $allocations->supervisors,
                            $supervisors->toArray()
                        );
                    }

                    foreach ($support->points as $point) {
                        $pointVaccinators = $point
                        ->vaccinators()
                        ->wherePivotIn('vaccinator_id', $workerIds)
                        ->get();

                        if (count($pointVaccinators) > 0) {
                            $point->load('point');

                            $allocations->pointVaccinators[] = $point;
                        }

                        $pointAnnotators = $point
                        ->annotators()
                        ->wherePivotIn('annotator_id', $workerIds)
                        ->get();

                        if (count($pointAnnotators) > 0) {
                            $point->load('point');

                            $allocations->pointAnnotators[] = $point;
                        }
                    }
                }
            }


            return $allocations;
        } catch (ValidationException $e) {
            $erros = $e->errors();
            return $this->error('failed', 501, $erros);
        }
    }
}
