<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Plataform;
use App\Models\PersonalRefreshTokens;
use App\Models\Ability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Exception;

class AuthenticatedController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'plataforma_uuid' => 'required',
                'plataforma_password' => 'required'
            ]);

            $plataform = Plataform::where(
                'uuid',
                '=',
                $request->plataforma_uuid
            )->where(
                'password',
                '=',
                $request->plataforma_password
            )->first();

            if (!$plataform) {
                throw ValidationException::withMessages([
                    'plataform' => ['Credenciais incorretas'],
                ]);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            $plataform->users()->findOrFail($user->id);

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Credenciais incorretas'],
                ]);
            }

            $user->tokens()->delete();

            $abilities = [];

            foreach ($user->abilities as $ability) {
                $abilities[] = $ability->name;
            }

            $token = $user->createToken($plataform->name, $abilities);
            $refreshToken = $user->createRefreshToken($plataform->name, $token);

            return $this->respondWithToken($token, $refreshToken, $user);
        } catch (ValidationException $e) {
            $erros = $e->errors();
            return $this->error('Login failed', 501, $erros);
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return true;
    }

    /**
     * Display the specified resource by token.
     *
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        $user = Auth::user();
        $user = User::with(['abilities'])->find($user->id);
        $user->is_superuser = false;

        foreach ($user->abilities as $ability) {
            if ($ability->name == 'zoonoses:admin') {
                $user->is_superuser = true;
            }
        }

        return $this->success($user);
        /*if (!Gate::authorize('is-admin', $user)) {
        return response()->json(['error' => 'Not authorized.'], 403);
        } */
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        try {
            $request->validate([
                'refresh_token' => 'required'
            ]);

            $refreshToken = PersonalRefreshTokens::findToken($request->get('refresh_token'));
            if (!$refreshToken) {
                throw ValidationException::withMessages([
                    'refresh_token' => ['Credenciais incorretas'],
                ]);
            }
            $user = $refreshToken->tokenable_type::find($refreshToken->tokenable_id);
            $token = PersonalAccessToken::findOrFail($refreshToken->token_id);
            $abilities = (array) $token->abilities;
            $plataformName = $token->name;

            $refreshToken->delete();
            $token->delete();

            $token = $user->createToken($plataformName, $abilities);
            $refreshToken = $user->createRefreshToken($plataformName, $token);

            return $this->respondWithToken($token, $refreshToken, $user);
        } catch (ValidationException $e) {
            $erros = $e->errors();
            return $this->error('Refresh token failed', 501, $erros);
        }
    }

    protected function respondWithToken($token, $refreshToken, $user)
    {
        return $this->success(
            [
                'access_token' => $token->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'token_type' => 'bearer',
                'expires_in' => config('sanctum.expiration') * 60,
                'user' => $user
            ]
        );
    }
}
