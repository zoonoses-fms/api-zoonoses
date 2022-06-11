<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MetaPhone;

class TheBlock extends MetaPhone
{
    use HasFactory;

    public function neighborhood()
    {
        return $this->belongsTo(TheNeighborhood::class);
    }

    public function geography()
    {
        return $this->hasOne(TheBlockGeography::class);
    }
}
