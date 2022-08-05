<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Location\The\TheNeighborhoodAlias;

class VaccinationSupport extends Model
{
    use HasFactory, SoftDeletes;

    public function campaingSupports()
    {
        return $this->hasMany(CampaingSupport::class, 'campaing_support_id');
    }

    public function neighborhoodAlias()
    {
        return $this->belongsTo(TheNeighborhoodAlias::class, 'the_neighborhood_alias_id');
    }

    public function getNeighborhood()
    {
        if ($this->the_neighborhood_alias_id != null) {
            $neighborhoodAlias = $this->neighborhoodAlias;

            if ($this->the_neighborhood_id != null) {
                $neighborhood = $neighborhoodAlias->neighborhood;
                return $neighborhood->name;
            }
            return $neighborhoodAlias->name;
        }
        return '';
    }
}
