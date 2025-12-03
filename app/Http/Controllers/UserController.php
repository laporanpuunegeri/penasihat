<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Wajib ada
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::paginate(10); 
        return view('tetapan.pengguna.index', compact('users'));
    }

    public function create()
    {
        return view('auth.register'); 
    }

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
            
            // 1. Validasi Gambar Tandatangan (Wajib PNG, Max 2MB)
            'signature_file' => ['required', 'file', 'mimes:png', 'max:2048'],

            // 2. Validasi Peranan Unik per Negeri
            'role' => [
                'required', 
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $limitedRoles = ['eo', 'cc', 'pa', 'yb'];

                    if (in_array($value, $limitedRoles)) {
                        $exists = User::where('role', $value)
                                      ->where('negeri', $request->negeri)
                                      ->exists();

                        if ($exists) {
                            $fail("Peranan " . strtoupper($value) . " telah wujud untuk negeri " . $request->negeri . ". Hanya seorang dibenarkan.");
                        }
                    }
                },
            ],

            'password' => [
                'required', 
                'confirmed', 
                'min:8', 
                'max:12', 
                Password::default()
            ],
        ]);

        // 3. Proses Upload Gambar
        $signaturePath = null;
        if ($request->hasFile('signature_file')) {
            // Simpan di storage/app/public/signatures
            $signaturePath = $request->file('signature_file')->store('signatures', 'public');
        }

        // 4. Simpan User
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

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if (strtolower($user->role) === 'super_admin') {
            return response()->json(['status' => 'error', 'message' => 'Tidak boleh memadam Super Admin.'], 403);
        }
        
        // Hapus fail tandatangan jika ada
        if ($user->signature_file && Storage::disk('public')->exists($user->signature_file)) {
             Storage::disk('public')->delete($user->signature_file);
        }

        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'Pengguna berjaya dipadam.']);
    }
}