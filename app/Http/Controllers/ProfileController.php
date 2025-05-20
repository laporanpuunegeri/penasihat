<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_telefon' => 'nullable|string|max:20',
            'negeri' => 'nullable|string|max:100',
            'nama_jawatan' => 'nullable|string|max:255',
            'gred_jawatan' => 'nullable|string|max:50',
            'bahagian' => 'nullable|string|max:255',
            'role' => 'required|in:user,pa,yb',
        ]);

        $user = Auth::user();
        $user->update($request->only([
            'name', 'email', 'no_telefon', 'negeri',
            'nama_jawatan', 'gred_jawatan', 'bahagian', 'role'
        ]));

        return redirect()->route('profile.show')->with('success', 'Profil berjaya dikemaskini.');
    }
}
