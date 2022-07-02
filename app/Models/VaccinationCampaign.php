<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationCampaign extends Model
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
        'goal'
    ];

    public function supports()
    {
        return $this->hasMany(VaccinationCampaingSupport::class, 'vaccination_campaign_id');
    }
}
