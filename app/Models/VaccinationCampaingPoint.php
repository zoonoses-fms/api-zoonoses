<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationCampaingPoint extends Model
{
    use HasFactory;

    public function campaingSupport()
    {
        return $this->belongsTo(VaccinationSupport::class, 'vaccination_campaing_support_id');
    }

    public function point()
    {
        return $this->belongsTo(VaccinationPoint::class, 'vaccination_point_id');
    }
}
