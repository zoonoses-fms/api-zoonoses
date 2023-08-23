<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;

class ProfileWorker extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'scope',
        'management',
        'is_single_allocation',
        'is_pre_campaign',
        'is_multiple',
        'is_rural',
    ];

    public function campaignProfileWorkers()
    {
        return $this->hasMany(CampaignProfileWorker::class, 'profile_workers_id');
    }

    public function campaigns()
    {
        return $this
            ->belongsToMany(Campaign::class, 'campaign_profile_workers', 'profile_workers_id', 'campaign_id');
    }

    public function workersAll()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'campaign_worker',
            'profile_workers_id',
            'vaccination_worker_id'
        )->orderBy('name', 'asc');
    }

    protected function queryBuilder(
        $query,
        $campaign_id = null,
        $campaign_cycle_id = null,
        $campaign_support_id = null,
        $campaign_point_id = null
    ) {
        $query->when(
            $campaign_id != null,
            function ($query) use ($campaign_id) {
                $query->where('campaign_worker.campaign_id', $campaign_id);
            },
            function ($query) {
                $query->whereNull('campaign_worker.campaign_id');
            }
        )->when(
            $campaign_cycle_id != null,
            function ($query) use ($campaign_cycle_id) {
                $query->where('campaign_worker.campaign_cycle_id', $campaign_cycle_id);
            },
            function ($query) {
                $query->whereNull('campaign_worker.campaign_cycle_id');
            }
        )->when(
            $campaign_support_id != null,
            function ($query) use ($campaign_support_id) {
                $query->where('campaign_worker.campaign_support_id', $campaign_support_id);
            },
            function ($query) {
                $query->whereNull('campaign_worker.campaign_support_id');
            }
        )->when(
            $campaign_point_id != null,
            function ($query) use ($campaign_point_id) {
                $query->where('campaign_worker.campaign_point_id', $campaign_point_id);
            },
            function ($query) {
                $query->whereNull('campaign_worker.campaign_point_id');
            }
        );

        return $query;
    }

    public function loadWorkers(
        $campaign_id = null,
        $campaign_cycle_id = null,
        $campaign_support_id = null,
        $campaign_point_id = null
    ) {
        for ($i = 0; $i <= $this->is_pre_campaign; $i++) {
            $workers[$i] = $this->queryBuilder(
                $this->workersAll()->wherePivot('is_pre_campaign', $i),
                $campaign_id,
                $campaign_cycle_id,
                $campaign_support_id,
                $campaign_point_id
            )->withPivot('id', 'is_pre_campaign', 'is_confirmation', 'is_presence')->get();

            foreach ($workers[$i] as $worker) {
                $worker->label = "{$worker->id} : {$worker->name}";
            }
        }
        /*
        if ($this->is_pre_campaign) {
            $workers = [];

            $preCampaign = $this->queryBuilder(
                $this->workersAll()->wherePivot('is_pre_campaign', true),
                $campaign_id,
                $campaign_cycle_id,
                $campaign_support_id,
                $campaign_point_id
            );

            $campaign = $this->queryBuilder(
                $this->workersAll()->wherePivot('is_pre_campaign', false),
                $campaign_id,
                $campaign_cycle_id,
                $campaign_support_id,
                $campaign_point_id
            );

            $workers[] = $preCampaign->withPivot('is_pre_campaign')->get();

            $workers[] = $campaign->withPivot('is_pre_campaign')->get();

            $this->workers = $workers;
        } else {
            $query = $this->queryBuilder(
                $this->workersAll(),
                $campaign_id,
                $campaign_cycle_id,
                $campaign_support_id,
                $campaign_point_id
            );

            $this->workers = $query->withPivot('is_pre_campaign')->get();
        }
         */

        $this->workers = $workers;
    }

    protected function profileMultiple($workers, $params, $is_pre_campaign)
    {
        $arrayWorkers = [];
        foreach ($workers as $worker) {
            $arrayWorkers[$worker['id']] = array_merge([
                'is_pre_campaign' => $is_pre_campaign,
                'created_at' => now(),
                'updated_at' => now()
            ], $params);
        }

        return $arrayWorkers;
    }

    protected function profileSingle($workers, $params, $is_pre_campaign)
    {
        $arrayWorkers = [];
        if (isset($workers['id'])) {
            $arrayWorkers[$workers['id']] = array_merge([
                'is_pre_campaign' => $is_pre_campaign,
                'created_at' => now(),
                'updated_at' => now()
            ], $params);
            return $arrayWorkers;
        } elseif (is_array($workers)) {
            foreach ($workers as $worker) {
                $arrayWorkers[$worker['id']] = array_merge([
                    'is_pre_campaign' => $is_pre_campaign,
                    'created_at' => now(),
                    'updated_at' => now()
                ], $params);
            }
            return $arrayWorkers;
        }
    }

    protected function syncWorkersCampaign(
        $workers,
        $is_pre_campaign,
        $campaign_id = null
    ) {
        $this->workersAll()
            ->wherePivot('campaign_id', $campaign_id)
            ->wherePivotNull('campaign_cycle_id')
            ->wherePivotNull('campaign_support_id')
            ->wherePivotNull('campaign_point_id')
            ->wherePivot('is_pre_campaign', $is_pre_campaign)
            ->sync($workers);
    }

    protected function syncWorkersCycle(
        $workers,
        $is_pre_campaign,
        $campaign_id = null,
        $campaign_cycle_id = null,
    ) {
        $this->workersAll()
            ->wherePivot('campaign_id', $campaign_id)
            ->wherePivot('campaign_cycle_id', $campaign_cycle_id)
            ->wherePivotNull('campaign_support_id')
            ->wherePivotNull('campaign_point_id')
            ->wherePivot('is_pre_campaign', $is_pre_campaign)
            ->sync($workers);
    }

    protected function syncWorkersSupport(
        $workers,
        $is_pre_campaign,
        $campaign_id = null,
        $campaign_cycle_id = null,
        $campaign_support_id = null
    ) {
        $this->workersAll()
            ->wherePivot('campaign_id', $campaign_id)
            ->wherePivot('campaign_cycle_id', $campaign_cycle_id)
            ->wherePivot('campaign_support_id', $campaign_support_id)
            ->wherePivotNull('campaign_point_id')
            ->wherePivot('is_pre_campaign', $is_pre_campaign)
            ->sync($workers);
    }

    public function syncWorkersPoint(
        $workers,
        $is_pre_campaign,
        $campaign_id = null,
        $campaign_cycle_id = null,
        $campaign_support_id = null,
        $campaign_point_id = null
    ) {
        $this->workersAll()
            ->wherePivot('campaign_id', $campaign_id)
            ->wherePivot('campaign_cycle_id', $campaign_cycle_id)
            ->wherePivot('campaign_support_id', $campaign_support_id)
            ->wherePivot('campaign_point_id', $campaign_point_id)
            ->wherePivot('is_pre_campaign', $is_pre_campaign)
            ->sync($workers);
    }

    protected function selectSync(
        $workers,
        $is_pre_campaign,
        $campaign_id = null,
        $campaign_cycle_id = null,
        $campaign_support_id = null,
        $campaign_point_id = null
    ) {
        if ($campaign_point_id !== null && $campaign_support_id !== null && $campaign_cycle_id !== null && $campaign_id !== null) {
            return $this->syncWorkersPoint($workers, $is_pre_campaign, $campaign_id, $campaign_cycle_id, $campaign_support_id, $campaign_point_id);
        }

        if ($campaign_support_id !== null && $campaign_cycle_id !== null && $campaign_id !== null) {
            return $this->syncWorkersSupport($workers, $is_pre_campaign, $campaign_id, $campaign_cycle_id, $campaign_support_id);
        }

        if ($campaign_cycle_id !== null && $campaign_id !== null) {
            return $this->syncWorkersCycle($workers, $is_pre_campaign, $campaign_id, $campaign_cycle_id);
        }

        if ($campaign_id !== null) {
            return $this->syncWorkersCampaign($workers, $is_pre_campaign, $campaign_id);
        }
    }

    public function updateWorker(
        $profile,
        $campaign_id = null,
        $campaign_cycle_id = null,
        $campaign_support_id = null,
        $campaign_point_id = null
    ) {
        $params = [];

        if ($campaign_id !== null) {
            $params['campaign_id'] = $campaign_id;
        } else {
            return false;
        }

        $params['campaign_cycle_id'] = $campaign_cycle_id;

        $params['campaign_support_id'] = $campaign_support_id;

        $params['campaign_point_id'] = $campaign_point_id;

        $params['is_single_allocation'] = $profile['is_single_allocation'];

        for ($i = 0; $i <= $profile['is_pre_campaign']; $i++) {
            if ($profile['is_multiple']) {
                $workers = $this->profileMultiple($profile['workers'][$i], $params, $i);
                $this->selectSync(
                    $workers,
                    $i,
                    $campaign_id,
                    $campaign_cycle_id,
                    $campaign_support_id,
                    $campaign_point_id
                );
            } else {
                $workers = $this->profileSingle($profile['workers'][$i], $params, $i);
                $this->selectSync(
                    $workers,
                    $i,
                    $campaign_id,
                    $campaign_cycle_id,
                    $campaign_support_id,
                    $campaign_point_id
                );
            }
        }
    }
}
