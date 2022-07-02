<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaccinationSupport extends Model
{
    use HasFactory, SoftDeletes;

    public function campaingSupports()
    {
        return $this->hasMany(VaccinationCampaingSupport::class, 'vaccination_support_id');
    }
}
