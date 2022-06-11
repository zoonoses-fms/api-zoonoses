<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class NewRefreshToken implements Arrayable, Jsonable
{
    /**
     * The access token instance.
     *
     * @var PersonalRefreshTokens
     */
    public $accessToken;

    /**
     * The plain text version of the token.
     *
     * @var string
     */
    public $plainTextToken;

    /**
     * Create a new access token result.
     *
     * @param  PersonalRefreshTokens  $refreshToken
     * @param  string  $plainTextToken
     * @return void
     */
    public function __construct(PersonalRefreshTokens $refreshToken, string $plainTextToken)
    {
        $this->refreshToken = $refreshToken;
        $this->plainTextToken = $plainTextToken;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'refreshToken' => $this->refreshToken,
            'plainTextToken' => $this->plainTextToken,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
