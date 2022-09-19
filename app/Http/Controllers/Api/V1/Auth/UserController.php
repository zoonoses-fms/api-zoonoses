<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Core;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        if (!$request->user()->tokenCan('zoonoses:admin')) {
            return response()->json(['error' => 'Not authorized.'], 401);
        }

        if ($request->has('core')) {
            $core = Core::find($request->get('core'));
            $users = $core->users;
            return $users;
        }

        if ($request->has('per_page')) {
            $perPage = $request->input('per_page');
        } else {
            $perPage = 5;
        }

        if ($request->has('search') && strlen($request->search) > 0) {
            $search = $request->search;
            $users = User::with(['plataforms', 'cores', 'abilities'])->where('name', 'ilike', '%' . $search . '%')->paginate($perPage);
        } else {
            $users = User::with(['plataforms', 'cores', 'abilities'])->paginate($perPage);
        }

        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->user()->tokenCan('zoonoses:admin')) {
            return response()->json(['error' => 'Not authorized.'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->plataforms()->syncWithPivotValues($request->plataforms, ['created_at' => now(), 'updated_at' => now()]);
        $user->cores()->syncWithPivotValues($request->cores, ['created_at' => now(), 'updated_at' => now()]);
        $user->abilities()->syncWithPivotValues($request->abilities, ['created_at' => now(), 'updated_at' => now()]);

        $user->save();

        event(new Registered($user));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with(['plataforms', 'cores', 'abilities'])->find($id);
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $requestUser = $request->user();

        $user = User::with(['abilities'])->find($id);

        if (!$request->user()->tokenCan('zoonoses:admin')) {
            return response()->json(['error' => 'Not authorized.'], 401);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->plataforms()->syncWithPivotValues($request->plataforms, ['updated_at' => now()]);
        $user->cores()->syncWithPivotValues($request->cores, ['created_at' => now(), 'updated_at' => now()]);
        $user->abilities()->syncWithPivotValues($request->abilities, ['updated_at' => now()]);

        $user->save();
        $user = User::with(['plataforms', 'cores', 'abilities'])->find($id);

        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $requestUser = $request->user();

        $user = User::with(['abilities'])->find($id);

        if (!$request->user()->tokenCan('zoonoses:admin')) {
            return response()->json(['error' => 'Not authorized.'], 401);
        }

        $user->abilities()->detach();

        $user->cores()->detach();

        $user->plataforms()->detach();

        $user->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function checkEmail($email)
    {
        $user = User::where('email', $email)->first();
        if ($user == null) {
            return false;
        }
        return true;
    }
}
