<?php

namespace App\Http\Controllers\Api\V1\Ncrlo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VaccinationWorker;
use App\Models\CampaignCycle;
use App\Models\Plataform;
use Illuminate\Support\Facades\DB;
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
        $listTypeAll = array(
            'all'
        );

        if ($request->has('list_type')) {
            if (in_array($request->list_type, $listTypeAll)) {
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
                )->orderBy('name', 'asc')
                ->get();

                foreach ($workers as $worker) {
                    $worker->label = "{$worker->id} : {$worker->name}";
                }

                return $workers;
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
            'registration' => $request->registration,
            'type' => $request->type
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
        if ($request->has('change_type')) {
            $worker->type = $request->change_type;
            $worker->save();
            return $worker;
        }
        $worker->name = $request->name;
        $worker->cpf = $request->cpf;
        $worker->phone = $request->phone;
        $worker->registration = $request->registration;
        $worker->type = $request->type;

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
        try {
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
                $allocation = DB::table('campaign_worker')
                    ->select(
                        'vaccination_workers.id as id',
                        'vaccination_workers.name as name',
                        'profile_workers.name as occupation',
                        'campaign_worker.campaign_id as campaign_id',
                        'campaign_cycles.id as cycle_id',
                        'campaign_cycles.description as cycle_name',
                        'campaign_supports.id as support_id',
                        'vaccination_supports.name as support_name',
                        'campaign_points.id as point_id',
                        'vaccination_points.name as point_name',
                    )
                    ->join('vaccination_workers', 'campaign_worker.vaccination_worker_id', '=', 'vaccination_workers.id')
                    ->join('profile_workers', 'campaign_worker.profile_workers_id', '=', 'profile_workers.id')
                    ->leftJoin('campaign_cycles', 'campaign_worker.campaign_cycle_id', '=', 'campaign_cycles.id')
                    ->leftJoin('campaign_supports', 'campaign_worker.campaign_support_id', '=', 'campaign_supports.id')
                    ->leftJoin('vaccination_supports', 'campaign_supports.vaccination_support_id', '=', 'vaccination_supports.id')
                    ->leftJoin('campaign_points', 'campaign_worker.campaign_point_id', '=', 'campaign_points.id')
                    ->leftJoin('vaccination_points', 'campaign_points.vaccination_point_id', '=', 'vaccination_points.id')
                    ->where('campaign_worker.campaign_cycle_id', $cycle->id)
                    ->where('campaign_worker.vaccination_worker_id', $worker->id)
                    ->get()->toArray();

                $allocations = array_merge($allocation, $allocations);
            }

            return $allocations;
        } catch (\Throwable $th) {
            return [];
        }
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
