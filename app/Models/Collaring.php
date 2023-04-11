<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaring extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'team_id',
        'the_saad_id',
        'the_neighborhood_id',
        'the_block_id',
        'the_sub_location_id',
        'address',
        'address_number',
        'landmark',
        'owner_name',
        'phone'
    ];
}
