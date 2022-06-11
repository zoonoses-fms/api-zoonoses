<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'occupation',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The refresh token the user is using for the current request.
     *
     * @var \Laravel\Sanctum\Contracts\HasAbilities
     */
    protected $refreshToken;


    public function plataforms()
    {
        return $this->belongsToMany(
            Plataform::class,
            'user_plataform',
            'user_id',
            'plataform_id'
        );
    }

    public function cores()
    {
        return $this->belongsToMany(
            Core::class,
            'user_core',
            'user_id',
            'core_id'
        );
    }

    public function abilities()
    {
        return $this->belongsToMany(
            Ability::class,
            'user_ability',
            'user_id',
            'ability_id'
        );
    }

    /**
     * Get the access tokens that belong to model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function refreshTokens()
    {
        return $this->morphMany(PersonalRefreshTokens::class, 'tokenable');
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $abilities
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createToken(string $name, array $abilities = ['*'])
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(128)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
    }

    /**
     * Create a new personal refresh token for the user.
     *
     * @param  string  $name
     * @param  array  $abilities
     * @return NewRefreshToken
     */
    public function createRefreshToken(string $name, $token)
    {
        $refreshToken = $this->refreshTokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(128)),
            'token_id' => $token->accessToken->id
        ]);

        return new NewRefreshToken($refreshToken, $refreshToken->getKey() . '|' . $plainTextToken);
    }
}
