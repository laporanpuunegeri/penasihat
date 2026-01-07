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
use Illuminate\Validation\ValidationException;

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
        // 1. Validasi awal
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_telefon' => ['nullable', 'string', 'max:20'],
            'negeri' => ['required', 'string', 'max:100'],
            'nama_jawatan' => ['nullable', 'string', 'max:255'],
            'gred_jawatan' => ['nullable', 'string', 'max:50'],
            'bahagian' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:user,eo,cc,pa,yb,super_admin'], 
            'signature_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'], 
        ]);

        if (in_array($request->role, ['pa', 'yb', 'cc', 'eo'])) {
            $existing = User::where('role', $request->role)
                            ->where('negeri', $request->negeri)
                            ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'role' => 'Sudah ada pengguna dengan peranan "' . strtoupper($request->role) . '" di negeri ini.',
                ]);
            }
        }

        // =========================================================
        // 🔥 2. PROSES UPLOAD TANDATANGAN (TUKAR KE BASE64) 🔥
        // =========================================================
        $signatureBase64 = null; 

        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            
            $fileContent = file_get_contents($file->getRealPath());
            
            $base64 = base64_encode($fileContent);
            $mimeType = $file->getMimeType();

            $signatureBase64 = 'data:' . $mimeType . ';base64,' . $base64;
        }

        // 3. Simpan pengguna ke Database
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
            'signature_file' => $signatureBase64, 
        ]);

        event(new Registered($user));
        return redirect()->route('tetapan.pengguna.index')->with('success', 'Pengguna berjaya didaftarkan bersama tandatangan!');
    }
}