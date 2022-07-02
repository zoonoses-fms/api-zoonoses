<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationCampaingSupport extends Model
{
    use HasFactory;

    public function campaing()
    {
        return $this->belongsTo(VaccinationCampaign::class, 'vaccination_campaign_id');
    }

    public function support()
    {
        return $this->belongsTo(VaccinationSupport::class, 'vaccination_support_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(VaccinationSupervisor::class, 'vaccination_supervisor_id');
    }

    public function points()
    {
        return $this->hasMany(VaccinationCampaingPoint::class, 'vaccination_campaing_support_id');
    }
}
