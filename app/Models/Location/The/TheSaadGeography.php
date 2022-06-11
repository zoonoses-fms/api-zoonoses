<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheSaadGeography extends Model
{
    use HasFactory;

    public function saad()
    {
        return $this->belongsTo(TheSaad::class);
    }

}
