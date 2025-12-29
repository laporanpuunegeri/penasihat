<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
     * Simpan pengguna baru ke database (DENGAN BASE64).
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
            
            // Validasi fail gambar biasa
            'signature_file' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],

            'role' => ['required', 'string'], 
            'password' => ['required', 'confirmed', 'min:8', 'max:12', Password::default()],
        ]);

        // 🔥 LOGIK BARU: Convert Gambar ke Base64
        $signatureData = null;
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            
            // 1. Dapatkan jenis fail (cth: image/png)
            $type = $file->getClientMimeType();
            
            // 2. Baca isi fail dan tukar jadi kod base64
            $data = base64_encode(file_get_contents($file));
            
            // 3. Gabungkan jadi string lengkap yang boleh dibaca browser
            $signatureData = 'data:' . $type . ';base64,' . $data;
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
            // Simpan data Base64 terus ke DB
            'signature_file' => $signatureData, 
        ]);

        return redirect()->route('tetapan.pengguna.index')->with('success', 'Akaun pengguna baru berjaya didaftarkan!');
    }

    /**
     * Papar borang edit.
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
     * Proses simpan kemaskini (DENGAN BASE64).
     */
    public function update(Request $request, $id)
    {
        $userToEdit = User::findOrFail($id);
        $currentUser = Auth::user();

        // --- SEKATAN KESELAMATAN ---
        if ($currentUser->role == 'super_admin') {
            if (strtoupper(trim($currentUser->negeri)) !== strtoupper(trim($userToEdit->negeri))) {
                abort(403, 'Akses Ditolak: Negeri tidak sepadan.');
            }
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'no_telefon' => ['required', 'string', 'max:20'],
            'nama_jawatan' => ['required', 'string', 'max:255'],
            'gred_jawatan' => ['required', 'string', 'max:50'],
            
            // Validasi fail gambar biasa
            'signature_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
            
            'password' => ['nullable', 'confirmed', 'min:8', 'max:12'],
        ]);

        // 🔥 LOGIK BARU: Update Base64 jika ada fail baru upload
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            
            // Convert ke Base64
            $type = $file->getClientMimeType();
            $data = base64_encode(file_get_contents($file));
            
            // Update terus ke object user (Ganti data lama)
            // Tak perlu delete fail lama sebab tiada fail fizikal
            $userToEdit->signature_file = 'data:' . $type . ';base64,' . $data;
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
            'role' => $request->role,
            'negeri' => $request->negeri ?? $userToEdit->negeri,
            'bahagian' => $request->bahagian ?? $userToEdit->bahagian,
            // Nota: signature_file & password dah handle asing kat atas
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
             // return response()->json(['status' => 'error', 'message' => 'Tidak boleh memadam Super Admin.'], 403);
        }
        
        // 🔥 LOGIK BARU: Tak perlu padam fail dari storage
        // Sebab gambar disimpan dalam DB, bila user delete, gambar automatik hilang.

        $user->delete();
        
        if (request()->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Pengguna berjaya dipadam.']);
        }
        
        return redirect()->back()->with('success', 'Pengguna berjaya dipadam.');
    }
}