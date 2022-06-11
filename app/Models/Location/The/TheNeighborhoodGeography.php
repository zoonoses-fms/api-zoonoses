<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheNeighborhoodGeography extends Model
{
    use HasFactory;

    public function neighborhood()
    {
        return $this->belongsTo(TheNeighborhood::class);
    }
}
