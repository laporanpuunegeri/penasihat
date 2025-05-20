<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Paparkan borang daftar.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Proses daftar pengguna baharu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_telefon' => ['nullable', 'string', 'max:20'],
            'negeri' => ['nullable', 'string', 'max:100'],
            'nama_jawatan' => ['nullable', 'string', 'max:255'],
            'gred_jawatan' => ['nullable', 'string', 'max:50'],
            'bahagian' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:user,pa,yb'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telefon' => $request->no_telefon,
            'negeri' => $request->negeri,
            'nama_jawatan' => $request->nama_jawatan,
            'gred_jawatan' => $request->gred_jawatan,
            'bahagian' => $request->bahagian,
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
