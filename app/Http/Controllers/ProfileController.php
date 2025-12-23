<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return redirect()->route('profile.edit');
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id], 
            'no_telefon' => ['required', 'string', 'max:20'],
            'nama_jawatan' => ['required', 'string', 'max:255'],
            'gred_jawatan' => ['required', 'string', 'max:50'],
            
            // NOTA: Saya dah BUANG validation 'negeri' & 'bahagian' kat sini
            // supaya Controller tak harap data tu dihantar.

            'signature_file' => ['nullable', 'file', 'mimes:png', 'max:2048'], 
            'current_password' => ['nullable', 'required_with:new_password', 'current_password:web'],
            'new_password' => ['nullable', 'min:8', 'max:12', 'confirmed', 'exclude_if:current_password,null', Password::default()],
        ]);
        
        // UPDATE DATA (Hanya yang dibenarkan)
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_telefon' => $validated['no_telefon'],
            'nama_jawatan' => $validated['nama_jawatan'],
            'gred_jawatan' => $validated['gred_jawatan'],
            
            // JANGAN letak 'negeri' dan 'bahagian' kat sini.
            // Biar database kekal dengan nilai asal (yang Super Admin set).
        ]);
        
        // Proses Tukar Tandatangan
        if ($request->hasFile('signature_file')) {
            if ($user->signature_file && Storage::disk('public')->exists($user->signature_file)) {
                Storage::disk('public')->delete($user->signature_file);
            }

            $path = $request->file('signature_file')->store('signatures', 'public');
            $user->signature_file = $path;
        }

        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }
        
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil dan Tandatangan berjaya dikemaskini.');
    }
}