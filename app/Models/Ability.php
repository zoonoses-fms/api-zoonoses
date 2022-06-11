<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description'
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_abilitie',
            'abilitie_id',
            'user_id'
        );
    }

    /**
     * Get the core.
     */
    public function core()
    {
        return $this->belongsTo(Core::class);
    }
}
