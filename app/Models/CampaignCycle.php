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
}
