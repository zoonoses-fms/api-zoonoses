<?php

namespace App\Models;

use FilterIterator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'number',
        'core_id',
        'user_id'
    ];

    /**
     * Get the post that owns the comment.
     */
    public function core()
    {
        return $this->belongsTo(Core::class);
    }
}
