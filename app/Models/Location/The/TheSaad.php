<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MetaPhone;

class TheSaad extends MetaPhone
{
    use HasFactory;


    public function neighborhoods()
    {
        return $this->hasMany(TheNeighborhood::class);
    }

    public function geography()
    {
        return $this->hasOne(TheSaadGeography::class);
    }
}
