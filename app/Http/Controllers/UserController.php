<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Papar senarai pengguna.
     */
    public function index(Request $request)
    {
        // Pagination 10 orang per page
        $users = User::paginate(10); 
        return view('tetapan.pengguna.index', compact('users'));
    }

    /**
     * Papar borang daftar pengguna baru.
     */
    public function create()
    {
        return view('auth.register'); 
    }

    /**
     * Simpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_telefon' => ['required', 'string', 'max:20'],
            'negeri' => ['required', 'string'],
            'bahagian' => ['required', 'string'],
            'nama_jawatan' => ['required', 'string', 'max:255'],
            'gred_jawatan' => ['required', 'string', 'max:50'],
            
            // Tandatangan WAJIB masa daftar baru
            'signature_file' => ['required', 'file', 'mimes:png', 'max:2048'],

            'role' => ['required', 'string'], // Boleh tambah validation had role jika perlu
            'password' => ['required', 'confirmed', 'min:8', 'max:12', Password::default()],
        ]);

        // Proses Upload Gambar
        $signaturePath = null;
        if ($request->hasFile('signature_file')) {
            $signaturePath = $request->file('signature_file')->store('signatures', 'public');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telefon' => $request->no_telefon,
            'negeri' => $request->negeri,
            'bahagian' => $request->bahagian,
            'nama_jawatan' => $request->nama_jawatan,
            'gred_jawatan' => $request->gred_jawatan,
            'role' => $request->role,
            'signature_file' => $signaturePath, 
        ]);

        return redirect()->route('tetapan.pengguna.index')->with('success', 'Akaun pengguna baru berjaya didaftarkan!');
    }

    /**
     * Papar borang edit (FUNGSI INI YANG HILANG TADI).
     */
    public function edit($id)
    {
        $userToEdit = User::findOrFail($id);
        $currentUser = Auth::user();

        // --- SEKATAN: Super Admin hanya boleh edit staf NEGERI SAMA ---
        if ($currentUser->role == 'super_admin') {
            if (strtoupper(trim($currentUser->negeri)) !== strtoupper(trim($userToEdit->negeri))) {
                return redirect()->route('tetapan.pengguna.index')
                    ->with('error', 'Maaf, anda hanya dibenarkan mengemaskini pengguna Negeri ' . $currentUser->negeri . ' sahaja.');
            }
        }

        return view('tetapan.pengguna.edit', compact('userToEdit'));
    }

    /**
     * Proses simpan kemaskini (FUNGSI INI JUGA PENTING).
     */
    public function update(Request $request, $id)
    {
        $userToEdit = User::findOrFail($id);
        $currentUser = Auth::user();

        // --- SEKATAN KESELAMATAN (Double Check) ---
        if ($currentUser->role == 'super_admin') {
            if (strtoupper(trim($currentUser->negeri)) !== strtoupper(trim($userToEdit->negeri))) {
                abort(403, 'Akses Ditolak: Negeri tidak sepadan.');
            }
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id], // Email unik kecuali diri sendiri
            'no_telefon' => ['required', 'string', 'max:20'],
            'nama_jawatan' => ['required', 'string', 'max:255'],
            'gred_jawatan' => ['required', 'string', 'max:50'],
            
            // Tandatangan OPTIONAL masa update
            'signature_file' => ['nullable', 'file', 'mimes:png', 'max:2048'],
            
            // Password OPTIONAL masa update
            'password' => ['nullable', 'confirmed', 'min:8', 'max:12'],
        ]);

        // Update Tandatangan jika ada fail baru
        if ($request->hasFile('signature_file')) {
            // Padam fail lama
            if ($userToEdit->signature_file && Storage::disk('public')->exists($userToEdit->signature_file)) {
                Storage::disk('public')->delete($userToEdit->signature_file);
            }
            $userToEdit->signature_file = $request->file('signature_file')->store('signatures', 'public');
        }

        // Update Password jika diisi
        if ($request->filled('password')) {
            $userToEdit->password = Hash::make($request->password);
        }

        // Update Data Lain
        $userToEdit->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_telefon' => $request->no_telefon,
            'nama_jawatan' => $request->nama_jawatan,
            'gred_jawatan' => $request->gred_jawatan,
            'role' => $request->role, // Admin boleh tukar role
            'negeri' => $request->negeri ?? $userToEdit->negeri, // Jika form hantar negeri, update. Jika tak (super admin), kekal lama.
            'bahagian' => $request->bahagian ?? $userToEdit->bahagian,
        ]);

        return redirect()->route('tetapan.pengguna.index')->with('success', 'Data pengguna berjaya dikemaskini.');
    }

    /**
     * Padam pengguna.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Halang padam Super Admin (untuk keselamatan asas)
        if (strtolower($user->role) === 'super_admin' && Auth::user()->id != $user->id) {
             // Optional: Boleh benarkan padam jika perlu, tapi hati-hati
             // return response()->json(['status' => 'error', 'message' => 'Tidak boleh memadam Super Admin.'], 403);
        }
        
        if ($user->signature_file && Storage::disk('public')->exists($user->signature_file)) {
             Storage::disk('public')->delete($user->signature_file);
        }

        $user->delete();
        
        // Jika request datang dari fetch/ajax (butang merah tadi)
        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Pengguna berjaya dipadam.']);
        }
        
        return redirect()->back()->with('success', 'Pengguna berjaya dipadam.');
    }
}