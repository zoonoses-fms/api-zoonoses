<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location\The\TheSaad;

class CampaingSupport extends Model
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

    public function coordinator()
    {
        return $this->belongsTo(VaccinationWorker::class, 'coordinator_id');
    }

    public function supervisors()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'supervisor_support',
            'campaing_support_id',
            'supervisor_id'
        );
    }

    public function drivers()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'driver_support',
            'campaing_support_id',
            'driver_id'
        );
    }

    public function assistants()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'assistant_support',
            'campaing_support_id',
            'assistant_id'
        );
    }

    public function vaccinators()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'vaccinator_support',
            'campaing_support_id',
            'vaccinator_id'
        );
    }

    public function saads()
    {
        return $this->belongsToMany(
            TheSaad::class,
            'saad_support',
            'campaing_support_id',
            'saad_id'
        );
    }

    public function points()
    {
        return $this->hasMany(CampaingPoint::class, 'campaing_support_id');
    }

    public function ruralSupervisors()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'rural_supervisor_support',
            'campaing_support_id',
            'rural_supervisor_id'
        );
    }

    public function ruralAssistants()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'rural_assistant_support',
            'campaing_support_id',
            'rural_assistant_id'
        );
    }
}
