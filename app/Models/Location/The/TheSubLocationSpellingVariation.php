<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheSubLocationSpellingVariation extends Model
{
    use HasFactory;

    public function subLocation()
    {
        return $this->belongsTo(TheSubLocation::class);
    }
}
