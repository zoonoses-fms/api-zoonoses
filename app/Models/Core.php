<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Core extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'initial'
    ];

    /**
     * Get the abilities.
     */
    public function abilities()
    {
        return $this->hasMany(Ability::class);
    }

    /**
     * Get the teams.
     */
    public function teams()
    {
        return $this->hasMany(Teams::class);
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_core',
            'core_id',
            'user_id'
        );
    }
}
