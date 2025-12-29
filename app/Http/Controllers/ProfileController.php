<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Storage; // Tak perlu Storage dah
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
            
            // Validasi fail
            'signature_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'], 
            
            'current_password' => ['nullable', 'required_with:new_password', 'current_password:web'],
            'new_password' => ['nullable', 'min:8', 'max:12', 'confirmed', 'exclude_if:current_password,null', Password::default()],
        ]);
        
        // UPDATE DATA (Kekalkan data asas)
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_telefon' => $validated['no_telefon'],
            'nama_jawatan' => $validated['nama_jawatan'],
            'gred_jawatan' => $validated['gred_jawatan'],
        ]);
        
        // 🔥 LOGIK BARU: Convert Gambar ke Base64 (Untuk Profil Sendiri)
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            
            // 1. Dapatkan jenis fail
            $type = $file->getClientMimeType();
            
            // 2. Baca isi fail dan tukar jadi kod base64
            $data = base64_encode(file_get_contents($file));
            
            // 3. Simpan string panjang ni dalam database
            $user->signature_file = 'data:' . $type . ';base64,' . $data;
        }

        // Update Password jika ada
        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }
        
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil dan Tandatangan berjaya dikemaskini.');
    }
}