<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignCycle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'campaign_cycles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'campaign_id',
        'number',
        'description',
        'start',
        'end'
    ];

    public function campaing()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function supports()
    {
        return $this->hasMany(CampaingSupport::class, 'campaign_cycle_id')->orderBy('order', 'asc');
    }

    public function statisticCoordinator()
    {
        return $this->belongsTo(VaccinationWorker::class, 'statistic_coordinator_id');
    }

    public function statistics()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'statistic_cycle',
            'campaign_cycle_id',
            'statistic_id'
        );
    }

    public function coldChainCoordinator()
    {
        return $this->belongsTo(VaccinationWorker::class, 'cold_chain_coordinator_id');
    }

    public function coldChainNurse()
    {
        return $this->belongsTo(VaccinationWorker::class, 'cold_chain_nurse_id');
    }

    public function beforeColdChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'before_cold_chain_cycle',
            'campaign_cycle_id',
            'before_cold_chain_id'
        );
    }

    public function startColdChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'start_cold_chain_cycle',
            'campaign_cycle_id',
            'start_cold_chain_id'
        );
    }

    public function beforeDriverColdChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'before_driver_cold_chain_cycle',
            'campaign_cycle_id',
            'before_driver_cold_chain_id'
        );
    }

    public function startDriverColdChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'start_driver_cold_chain_cycle',
            'campaign_cycle_id',
            'start_driver_cold_chain_id'
        );
    }

    public function beforeTransports()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'before_transport_cycle',
            'campaign_cycle_id',
            'before_transport_id'
        );
    }

    public function startTransports()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'start_transport_cycle',
            'campaign_cycle_id',
            'start_transport_id'
        );
    }

    public function beforeZoonoses()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'before_zoonoses_cycle',
            'campaign_cycle_id',
            'before_zoonoses_id'
        );
    }

    public function startZoonoses()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'start_zoonoses_cycle',
            'campaign_cycle_id',
            'start_zoonoses_id'
        );
    }
}
