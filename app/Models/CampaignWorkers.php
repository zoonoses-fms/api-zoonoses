<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CampaignWorkers extends Pivot
{
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function cycle()
    {
        return $this->belongsTo(CampaignCycle::class, 'campaign_cycle_id');
    }

    public function support()
    {
        return $this->belongsTo(CampaignSupport::class, 'campaign_support_id');
    }

    public function point()
    {
        return $this->belongsTo(CampaignPoint::class, 'campaign_point_id');
    }

    public function profile()
    {
        return $this->belongsTo(ProfileWorker::class, 'profile_workers_id');
    }

    public function worker()
    {
        return $this->belongsTo(VaccinationWorker::class, 'vaccination_worker_id');
    }
}
