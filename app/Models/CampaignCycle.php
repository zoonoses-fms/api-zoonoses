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
        return $this->hasMany(CampaingSupport::class, 'campaign_cycle_id')->orderBy('updated_at', 'desc');
    }

    public function payrolls()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'payroll_cycle',
            'campaign_cycle_id',
            'payroll_id'
        );
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

    public function transports()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'transport_cycle',
            'campaign_cycle_id',
            'transport_id'
        );
    }


    public function coldChains()
    {
        return $this->belongsToMany(
            VaccinationWorker::class,
            'cold_chain_cycle',
            'campaign_cycle_id',
            'cold_chain_id'
        );
    }
}
