<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location\The\TheSaad;

class CampaignSupport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'campaign_id',
        'vaccination_support_id',
        'vaccination_worker_id',
        'goal',
        'car'
    ];

    public function cycle()
    {
        return $this->belongsTo(CampaignCycle::class, 'campaign_cycle_id');
    }

    public function support()
    {
        return $this->belongsTo(VaccinationSupport::class, 'vaccination_support_id');
    }

    public function profiles($scope = 'support')
    {
        $campaign = Campaign::find($this->cycle->campaign_id);
        return $campaign->profiles($scope);
    }

    public function loadProfiles($scope = 'support')
    {
        $cycle = CampaignCycle::select('id', 'campaign_id')->find($this->campaign_cycle_id);
        $campaign = Campaign::select('id')->find($cycle->campaign_id);
        $this->profiles =  $campaign->profiles($scope)->where('is_rural', $this->is_rural)->orderBy('created_at', 'desc')->get();
        foreach ($this->profiles as $profile) {
            $profile->loadWorkers($campaign->id, $cycle->id, $this->id);
        }
    }

    public function loadListWorkers($dates)
    {
        if (!isset($this->profiles)) {
            $this->loadProfiles($dates);
        }

        $list = [];
        foreach ($this->profiles as $profile) {
            $cost = [];
            $cost_total = 0;
            $listWorkers = [];

            for ($i=0; $i <= $profile->is_pre_campaign; $i++) {
                $cost[$i] = count($profile->workers[$i]) * (float)$profile->pivot->cost;

                if ($i > 0 && $this->partial_value) {
                    if ($cost[$i] > 0) {
                        $cost[$i] = (($cost[$i] / 100) * $this->percentage_value);
                    }
                }
                $cost_total += $cost[$i];

                foreach ($profile->workers[$i] as $key => $worker) {
                    if ($i > 0) {
                        $keyWorker = array_search($worker->registration, array_column($listWorkers, 'registration'));
                        if ($keyWorker === false) {
                            $arrayDays = [];

                            for ($indexDay = 0; $indexDay < $i; $indexDay++) {
                                $arrayDays[$indexDay] = 0.0;
                            }
                            $arrayDays[] = (float)$profile->pivot->cost;

                            $listWorkers[] = [
                                'registration' => $worker->registration,
                                'name' => $worker->name,
                                'profile' => $profile->name,
                                'days' => $arrayDays
                            ];
                        } else {
                            $listWorkers[$keyWorker]['days'][] = (float)$profile->pivot->cost;
                        }
                    } else {
                        $listWorkers[] = [
                            'registration' => $worker->registration,
                            'name' => $worker->name,
                            'profile' => $profile->name,
                            'days' => [(float)$profile->pivot->cost]
                        ];
                    }
                }
            }

            for ($i = 0; $i < count($dates); $i++) {
                for ($j = 0; $j < count($listWorkers); $j++) {
                    if (!array_key_exists($i, $listWorkers[$j]['days'])) {
                        $listWorkers[$j]['days'][$i] = 0.0;
                    }
                }
            }

            for ($i = 0; $i < count($listWorkers); $i++) {
                $total = 0;
                foreach ($listWorkers[$i]['days'] as $day) {
                    $total += $day;
                }
                $listWorkers[$i]['total'] = $total;
            }

            $profile->cost = $cost;
            $profile->cost_total = $cost_total;
            $profile->listWorkers = $listWorkers;
            $list = array_merge($list, $listWorkers);
        }
        return $list;
    }

    public function coordinator()
    {
        return $this->belongsTo(VaccinationWorker::class, 'coordinator_id')->orderBy('name', 'asc');
    }

    public function supervisors()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'supervisor_support',
            'campaign_support_id',
            'supervisor_id'
        )->orderBy('name', 'asc');
    }

    public function drivers()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'driver_support',
            'campaign_support_id',
            'driver_id'
        )->orderBy('name', 'asc');
    }

    public function assistants()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'assistant_support',
            'campaign_support_id',
            'assistant_id'
        )->orderBy('name', 'asc');
    }

    public function vaccinators()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'vaccinator_support',
            'campaign_support_id',
            'vaccinator_id'
        )->orderBy('name', 'asc');
    }

    public function saads()
    {
        return $this->belongsToMany(
            TheSaad::class,
            'saad_support',
            'campaign_support_id',
            'saad_id'
        );
    }

    public function points()
    {
        return $this->hasMany(CampaignPoint::class, 'campaign_support_id');
    }

    public function ruralSupervisors()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'rural_supervisor_support',
            'campaign_support_id',
            'rural_supervisor_id'
        )->orderBy('name', 'asc');
    }

    public function ruralAssistants()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'rural_assistant_support',
            'campaign_support_id',
            'rural_assistant_id'
        )->orderBy('name', 'asc');
    }
}
