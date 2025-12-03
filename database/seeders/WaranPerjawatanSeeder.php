<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaranPerjawatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan jadual dahulu (Reset)
        DB::table('waran_perjawatans')->truncate();

        // 2. Senarai Data Waran
        // ASUMSI: Semua isian asal adalah Lantikan Persekutuan, Negeri disetkan 0.
        $data = [
            // [jawatan, bil, persekutuan, negeri, kosong, nota]
            ['GRED UTAMA C (VU7)', 2, 2, 0, 0, ''],
            ['TIMBALAN PENDAKWA RAYA L13', 1, 1, 0, 0, ''],
            ['PENOLONG PENASIHAT UNDANG-UNDANG L12', 1, 1, 0, 0, ''],
            // Contoh yang isi > waran
            ['PEGAWAI SYARIAH LS12', 0, 0, 1, -1, 'Terdapat lantikan Negeri yang mengisi jawatan ini'], 
            ['PEGAWAI UNDANG-UNDANG L12', 1, 1, 0, 0, ''],
            ['TIMBALAN PENDAKWA RAYA L12', 1, 1, 0, 0, ''],
            ['PEGAWAI UNDANG-UNDANG L9/ L10', 2, 2, 0, 0, ''],
            ['TIMBALAN PENDAKWA RAYA L9/ L10', 14, 12, 0, 2, '2 kekosongan jawatan'],
            ['PEN. PEGAWAI UNDANG-UNDANG L5/ L6/ L7', 4, 4, 0, 0, ''],
            ['SETIAUSAHA PEJABAT N5/ N6/ N7', 1, 1, 0, 0, ''],
            ['PEN. PEGAWAI TADBIR N5/ N6/ N7', 1, 1, 0, 0, ''],
            ['PEN. PEG. TEKOLOGI MAKLUMAT FA5/FA6/FA7', 1, 1, 0, 0, ''],
            ['PEMBANTU TADBIR (P/O) N22/ N3', 1, 1, 0, 0, ''],
            ['PEMBANTU PUSTAKAWAN S1/ S2/ S3', 1, 1, 0, 0, ''],
            ['PEMBANTU TADBIR (KEW) W1/ W2/ W3', 1, 1, 0, 0, ''],
            ['PEMBANTU TADBIR (P/O) N1/ N2/ N3', 9, 9, 0, 0, ''],
            ['PEMBANTU KESELAMATAN KP1/ KP2/ KP3', 3, 2, 0, 1, ''],
            ['PEMBANTU KHIDMAT AM N1/ N2/ N3', 4, 4, 0, 0, ''],
        ];

        // 3. Masukkan data ke dalam database dengan timestamp
        foreach ($data as $item) {
            DB::table('waran_perjawatans')->insert([
                'jawatan'      => $item[0],
                'bil'          => $item[1],
                'persekutuan'  => $item[2], // FIELD BARU
                'negeri'       => $item[3], // FIELD BARU
                'isi'          => $item[2] + $item[3], // Total Isi
                'kosong'       => $item[4],
                'nota'         => $item[5], // FIELD BARU
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}