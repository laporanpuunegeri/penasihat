<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKesMahkamah;
use App\Models\LampiranKesMahkamah;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporankesmahkamahController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanKesMahkamah::query();
        $user = Auth::user();

        if ($request->filled('bulan')) {
            $query->whereMonth('tarikh_sebutan', $request->bulan)
                  ->whereYear('tarikh_sebutan', now()->year);
        }

        if ($user->role === 'pa' || $user->role === 'yb') {
            $query->where('negeri', $user->negeri);
        } elseif ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        $data = $query->orderBy('tarikh_sebutan', 'desc')->get();

        return view('laporankesmahkamah.index', compact('data'));
    }

    public function create()
    {
        return view('laporankesmahkamah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_kes' => 'required|string',
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required|string',
            'isu' => 'required|string',
            'skop_tugas' => 'required|string',
            'ringkasan_hujahan' => 'required|string',
            'status' => 'required|string',
        ]);

        $user = Auth::user();

        LaporanKesMahkamah::create(array_merge($validated, [
            'user_id' => $user->id,
            'negeri' => $user->negeri,
        ]));

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya disimpan.');
    }

    public function edit($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);
        return view('laporankesmahkamah.edit', compact('laporan'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_kes' => 'required|string',
            'tarikh_sebutan' => 'required|date',
            'fakta_ringkas' => 'required|string',
            'isu' => 'required|string',
            'skop_tugas' => 'required|string',
            'ringkasan_hujahan' => 'required|string',
            'status' => 'required|string',
        ]);

        $laporan = LaporanKesMahkamah::findOrFail($id);
        $laporan->update($validated);

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya dikemaskini.');
    }

    public function destroy($id)
    {
        $laporan = LaporanKesMahkamah::findOrFail($id);
        $laporan->delete();

        return redirect('/laporankesmahkamah')->with('success', 'Laporan berjaya dipadam.');
    }

    // === 💾 LAMPIRAN KES MAHKAMAH ===

    public function lampiran()
    {
        $user = Auth::user();
        $bulan = now()->month;
        $tahun = now()->year;

        $lampiran = LampiranKesMahkamah::where('user_id', $user->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('kategori'); // supaya boleh akses ikut nama kategori

        $kategori_list = [
            'Perlembagaan', 'Tanah / PBT', 'Rujukan tanah',
            'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan',
            'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain',
        ];

        return view('laporankesmahkamah.lampiran', compact('lampiran', 'kategori_list'));
    }

    public function simpanLampiran(Request $request)
    {
        $user = Auth::user();
        $bulan = now()->month;
        $tahun = now()->year;

        foreach ($request->data as $index => $row) {
            $kategori_list = [
                'Perlembagaan', 'Tanah / PBT', 'Rujukan tanah',
                'Undang-Undang Pentadbiran / Perkhidmatan', 'Kemalangan',
                'Perjanjian / Penswastaan', 'Pendakwaan', 'Lain-lain',
            ];

            $kategori = $kategori_list[$index];

            LampiranKesMahkamah::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'kategori' => $kategori
                ],
                [
                    'bil_aktif' => $row[0] ?? 0,
                    'majistret' => $row[1] ?? 0,
                    'sesi' => $row[2] ?? 0,
                    'tinggi' => $row[3] ?? 0,
                    'rayuan' => $row[4] ?? 0,
                    'persk' => $row[5] ?? 0,
                    'status' => $row[6] ?? '-',
                ]
            );
        }

        return redirect()->route('laporankesmahkamah.lampiran')->with('success', 'Lampiran berjaya dikemaskini.');
    }
}
