<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaranPerjawatanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kosongkan jadual dahulu (Reset data lama)
        DB::table('waran_perjawatans')->truncate();

        // 2. Senarai Data Waran (Data dari coding lama abang)
        $data = [
            // [Jawatan, Bil Waran, Persekutuan, Negeri, Kosong, Nota]
            ['GRED UTAMA C (VU7)', 2, 2, 0, 0, null],
            ['TIMBALAN PENDAKWA RAYA L13', 1, 1, 0, 0, null],
            ['PENOLONG PENASIHAT UNDANG-UNDANG L12', 1, 1, 0, 0, null],
            ['PEGAWAI SYARIAH LS12', 0, 0, 1, -1, 'Terdapat lantikan Negeri yang mengisi jawatan ini'], 
            ['PEGAWAI UNDANG-UNDANG L12', 1, 1, 0, 0, null],
            ['TIMBALAN PENDAKWA RAYA L12', 1, 1, 0, 0, null],
            ['PEGAWAI UNDANG-UNDANG L9/ L10', 2, 2, 0, 0, null],
            ['TIMBALAN PENDAKWA RAYA L9/ L10', 14, 12, 0, 2, '2 kekosongan jawatan'],
            ['PEN. PEGAWAI UNDANG-UNDANG L5/ L6/ L7', 4, 4, 0, 0, null],
            ['SETIAUSAHA PEJABAT N5/ N6/ N7', 1, 1, 0, 0, null],
            ['PEN. PEGAWAI TADBIR N5/ N6/ N7', 1, 1, 0, 0, null],
            ['PEN. PEG. TEKOLOGI MAKLUMAT FA5/FA6/FA7', 1, 1, 0, 0, null],
            ['PEMBANTU TADBIR (P/O) N22/ N3', 1, 1, 0, 0, null],
            ['PEMBANTU PUSTAKAWAN S1/ S2/ S3', 1, 1, 0, 0, null],
            ['PEMBANTU TADBIR (KEW) W1/ W2/ W3', 1, 1, 0, 0, null],
            ['PEMBANTU TADBIR (P/O) N1/ N2/ N3', 9, 9, 0, 0, null],
            ['PEMBANTU KESELAMATAN KP1/ KP2/ KP3', 3, 2, 0, 1, null],
            ['PEMBANTU KHIDMAT AM N1/ N2/ N3', 4, 4, 0, 0, null],
        ];

        // 3. Masukkan data ke dalam database
        foreach ($data as $item) {
            DB::table('waran_perjawatans')->insert([
                'jawatan'       => $item[0],
                'bil'           => $item[1],       // Jumlah Waran
                'persekutuan'   => $item[2],       // Isi Persekutuan
                'negeri'        => $item[3],       // Isi Negeri
                'isi'           => $item[2] + $item[3], // Auto kira Jumlah Isi (P + N)
                'kosong'        => $item[4],       // Kosong (Manual dari data abang)
                'nota'          => $item[5],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}