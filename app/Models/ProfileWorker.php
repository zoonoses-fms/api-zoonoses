<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        );
    }

    public function loadWorkers(
        $campaign_id = null,
        $campaign_cycle_id = null,
        $campaign_support_id = null,
        $campaign_point_id = null
    ) {
        if ($this->is_pre_campaign) {
            $workers = [];

            $workers[] = $this->workersAll()
            ->withPivot('is_pre_campaign')->when(
                $campaign_id != null,
                function ($query) use ($campaign_id) {
                    $query->where('campaign_worker.campaign_id', $campaign_id);
                }
            )->when(
                $campaign_cycle_id != null,
                function ($query) use ($campaign_cycle_id) {
                    $query->where('campaign_worker.campaign_cycle_id', $campaign_cycle_id);
                }
            )->when(
                $campaign_support_id != null,
                function ($query) use ($campaign_support_id) {
                    $query->where('campaign_worker.campaign_support_id', $campaign_support_id);
                }
            )->when(
                $campaign_point_id != null,
                function ($query) use ($campaign_point_id){
                    $query->where('campaign_worker.campaign_point_id', $campaign_point_id);
                }
            )->wherePivot('is_pre_campaign', true)->get();

            $workers[] = $this->workersAll()
            ->withPivot('is_pre_campaign')->when(
                $campaign_id != null,
                function ($query) use ($campaign_id) {
                    $query->where('campaign_worker.campaign_id', $campaign_id);
                }
            )->when(
                $campaign_cycle_id != null,
                function ($query) use ($campaign_cycle_id) {
                    $query->where('campaign_worker.campaign_cycle_id', $campaign_cycle_id);
                }
            )->when(
                $campaign_support_id != null,
                function ($query) use ($campaign_support_id) {
                    $query->where('campaign_worker.campaign_support_id', $campaign_support_id);
                }
            )->when(
                $campaign_point_id != null,
                function ($query) use ($campaign_point_id) {
                    $query->where('campaign_worker.campaign_point_id', $campaign_point_id);
                }
            )->wherePivot('is_pre_campaign', false)->get();

            $this->workers = $workers;
        } else {
            $this->workers = $this->workersAll()
                ->withPivot('is_pre_campaign')->when(
                    $campaign_id != null,
                    function ($query) use ($campaign_id) {
                        $query->where('campaign_worker.campaign_id', $campaign_id);
                    }
                )->when(
                    $campaign_cycle_id != null,
                    function ($query) use ($campaign_cycle_id) {
                        $query->where('campaign_worker.campaign_cycle_id', $campaign_cycle_id);
                    }
                )->when(
                    $campaign_support_id != null,
                    function ($query) use ($campaign_support_id) {
                        $query->where('campaign_worker.campaign_support_id', $campaign_support_id);
                    }
                )->when(
                    $campaign_point_id != null,
                    function ($query) use ($campaign_point_id) {
                        $query->where('campaign_worker.campaign_point_id', $campaign_point_id);
                    }
                )->get();
        }
    }

    protected function profileMultiple($workers, $params, $is_pre_campaign = false)
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

    protected function profileSingle($workers, $params, $is_pre_campaign = false)
    {
        $arrayWorkers = [];
        if (isset($workers['id'])) {
            $arrayWorkers[$workers['id']] = array_merge([
                'is_pre_campaign' => $is_pre_campaign,
                'created_at' => now(),
                'updated_at' => now()
            ], $params);
            return $arrayWorkers;
        } else {
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

        if ($campaign_cycle_id !== null) {
            $params['campaign_cycle_id'] = $campaign_cycle_id;
        }

        if ($campaign_support_id !== null) {
            $params['campaign_support_id'] = $campaign_support_id;
        }

        if ($campaign_point_id !== null) {
            $params['campaign_point_id'] = $campaign_point_id;
        }

        $params['is_single_allocation'] = $profile['is_single_allocation'];

        if ($profile['is_multiple']) {
            if ($profile['is_pre_campaign'] && count($profile['workers']) == 2) {
                for ($i = 0; $i < count($profile['workers']); $i++) {
                    /**
                     * Os perfis que que tem pre_campaign=true são os compostos de
                     * 2 array de VacinationWorkers posição "0" são os VacinationWorkers da Pre campanha
                     * e os da posição "1" são os VacationWorkers do dia da campanha.
                     */
                    $workers = $this->profileMultiple($profile['workers'][$i], $params, ($i == 0) ? true : false);
                    $this->workersAll()->when(
                        $campaign_id != null,
                        function ($query) use ($campaign_id) {
                            $query->where('campaign_worker.campaign_id', $campaign_id);
                        }
                    )->when(
                        $campaign_cycle_id != null,
                        function ($query) use ($campaign_cycle_id) {
                            $query->where('campaign_worker.campaign_cycle_id', $campaign_cycle_id);
                        }
                    )->when(
                        $campaign_support_id != null,
                        function ($query) use ($campaign_support_id) {
                            $query->where('campaign_worker.campaign_support_id', $campaign_support_id);
                        }
                    )->when(
                        $campaign_point_id != null,
                        function ($query) use ($campaign_point_id) {
                            $query->where('campaign_worker.campaign_point_id', $campaign_point_id);
                        }
                    )->wherePivot('is_pre_campaign', ($i == 0) ? true : false)->sync($workers);
                }
            } else {
                $workers = $this->profileMultiple($profile['workers'], $params);
                $this->workersAll()->when(
                    $campaign_id != null,
                    function ($query) use ($campaign_id) {
                        $query->where('campaign_worker.campaign_id', $campaign_id);
                    }
                )->when(
                    $campaign_cycle_id != null,
                    function ($query) use ($campaign_cycle_id) {
                        $query->where('campaign_worker.campaign_cycle_id', $campaign_cycle_id);
                    }
                )->when(
                    $campaign_support_id != null,
                    function ($query) use ($campaign_support_id) {
                        $query->where('campaign_worker.campaign_support_id', $campaign_support_id);
                    }
                )->when(
                    $campaign_point_id != null,
                    function ($query) use ($campaign_point_id) {
                        $query->where('campaign_worker.campaign_point_id', $campaign_point_id);
                    }
                )->sync($workers);
            }
        } else {
            if ($profile['is_pre_campaign'] && count($profile['workers']) == 2) {
                for ($i = 0; $i < count($profile['workers']); $i++) {
                    /**
                     * Os perfis que que tem pre_campaign=true são os compostos de
                     * 2 array de VacinationWorkers posição "0" são os VacinationWorkers da Pre campanha
                     * e os da posição "1" são os VacationWorkers do dia da campanha.
                     */
                    $workers = $this->profileSingle($profile['workers'][$i], $params, ($i == 0) ? true : false);
                    $this->workersAll()->when(
                        $campaign_id != null,
                        function ($query) use ($campaign_id) {
                            $query->where('campaign_worker.campaign_id', $campaign_id);
                        }
                    )->when(
                        $campaign_cycle_id != null,
                        function ($query) use ($campaign_cycle_id) {
                            $query->where('campaign_worker.campaign_cycle_id', $campaign_cycle_id);
                        }
                    )->when(
                        $campaign_support_id != null,
                        function ($query) use ($campaign_support_id) {
                            $query->where('campaign_worker.campaign_support_id', $campaign_support_id);
                        }
                    )->when(
                        $campaign_point_id != null,
                        function ($query) use ($campaign_point_id) {
                            $query->where('campaign_worker.campaign_point_id', $campaign_point_id);
                        }
                    )->wherePivot('is_pre_campaign', ($i == 0) ? true : false)->sync($workers);
                }
            } else {
                $workers = $this->profileSingle($profile['workers'], $params);
                $this->workersAll()->when(
                    $campaign_id != null,
                    function ($query) use ($campaign_id) {
                        $query->where('campaign_worker.campaign_id', $campaign_id);
                    }
                )->when(
                    $campaign_cycle_id != null,
                    function ($query) use ($campaign_cycle_id) {
                        $query->where('campaign_worker.campaign_cycle_id', $campaign_cycle_id);
                    }
                )->when(
                    $campaign_support_id != null,
                    function ($query) use ($campaign_support_id) {
                        $query->where('campaign_worker.campaign_support_id', $campaign_support_id);
                    }
                )->when(
                    $campaign_point_id != null,
                    function ($query) use ($campaign_point_id) {
                        $query->where('campaign_worker.campaign_point_id', $campaign_point_id);
                    }
                )->sync($workers);
            }
        }
    }
}
