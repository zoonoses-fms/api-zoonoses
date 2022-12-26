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
        $support = $this->support;
        $cycle = $support->cycle;
        $campaign = $cycle->campaign;
        $this->profiles =  $campaign->profiles($scope)->orderBy('created_at', 'desc')->get();
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
