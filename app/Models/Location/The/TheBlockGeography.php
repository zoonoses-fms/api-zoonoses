<?php

namespace App\Models\Location\The;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheBlockGeography extends Model
{
    use HasFactory;

    public function block()
    {
        return $this->belongsTo(TheBlock::class);
    }
}
