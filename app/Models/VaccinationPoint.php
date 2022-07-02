<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaccinationPoint extends Model
{
    use HasFactory, SoftDeletes;

    public function campaingPoints()
    {
        return $this->hasMany(VaccinationCampaingPoint::class, 'vaccination_point_id');
    }
}
