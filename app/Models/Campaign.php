<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'year',
        'start',
        'end',
        'goal',
        'coordinator_cost',
        'supervisor_cost',
        'assistant_cost',
        'vaccinator_cost',
        'annotators_cost',
        'rural_supervisor_cost',
        'rural_assistant_cost',
        'vaccine_cost',
        'mileage_cost',
        'driver_cost',
        'coordinator_id'
    ];

    public function cycles()
    {
        return $this->hasMany(CampaignCycle::class, 'campaign_id')->orderBy('start', 'desc');
    }
}
