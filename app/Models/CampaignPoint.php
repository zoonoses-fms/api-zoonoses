<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignPoint extends Model
{
    use HasFactory;

    public function support()
    {
        return $this->belongsTo(CampaignSupport::class, 'campaign_support_id');
    }

    public function loadProfiles($scope = 'point')
    {
        $support = CampaignSupport::select('id', 'campaign_cycle_id')->find($this->campaign_support_id);
        $cycle = CampaignCycle::select('id', 'campaign_id')->find($support->campaign_cycle_id);
        $campaign = Campaign::select('id')->find($cycle->campaign_id);
        $this->profiles =  $campaign->profiles($scope)->orderBy('created_at', 'desc')->get();
        foreach ($this->profiles as $profile) {
            $profile->loadWorkers($campaign->id, $cycle->id, $support->id, $this->id);
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

    public function loadProfilesNotPreLoad($scope = 'point')
    {
        $support = $this->support;
        $cycle = $support->cycle;
        $campaign = $cycle->campaign;
        $this->profiles =  $campaign->profiles($scope)->where('is_pre_load', false)->orderBy('created_at', 'desc')->get();
        foreach ($this->profiles as $profile) {
            $profile->loadWorkers($campaign->id, $cycle->id, $support->id, $this->id);
        }
    }

    public function supervisor()
    {
        return $this->belongsTo(VaccinationWorker::class, 'supervisor_id')->orderBy('name', 'asc');
    }

    public function vaccinators()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'vaccinator_point',
            'campaign_point_id',
            'vaccinator_id'
        )->orderBy('name', 'asc');
    }

    public function annotators()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'annotator_point',
            'campaign_point_id',
            'annotator_id'
        )->orderBy('name', 'asc');
    }

    public function saads()
    {
        return $this->belongsToMany(
            TheSaad::class,
            'saad_point',
            'campaign_point_id',
            'saad_id'
        );
    }

    public function point()
    {
        return $this->belongsTo(VaccinationPoint::class, 'vaccination_point_id');
    }
}
