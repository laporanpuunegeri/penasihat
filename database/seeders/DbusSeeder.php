<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DbusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kosongkan table dahulu untuk elak duplicate
        DB::table('dbuses')->truncate();

        $tahun = 2026; // Berdasarkan dokumen PDF
        $now = Carbon::now();

        $data = [
            // =================================================================
            // OA10000: EMOLUMEN
            // =================================================================
            ['kod_objek' => 'OA10000', 'perkara' => 'EMOLUMEN', 'jenis' => 'OA', 'jumlah' => 3553600.00, 'tahun' => $tahun],

            // --- OS11000: GAJI DAN UPAHAN ---
            ['kod_objek' => 'OS11000', 'perkara' => 'GAJI DAN UPAHAN', 'jenis' => 'OS', 'jumlah' => 2764300.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL11101', 'perkara' => 'Gaji Biasa Kakitangan Awam', 'jenis' => 'OL', 'jumlah' => 2764300.00, 'tahun' => $tahun],

            // --- OS12000: ELAUN DAN IMBUHAN TETAP ---
            ['kod_objek' => 'OS12000', 'perkara' => 'ELAUN DAN IMBUHAN TETAP', 'jenis' => 'OS', 'jumlah' => 663500.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL12101', 'perkara' => 'Elaun Khidmat Awam', 'jenis' => 'OL', 'jumlah' => 63800.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL12102', 'perkara' => 'Elaun Bantuan Sewa Rumah', 'jenis' => 'OL', 'jumlah' => 208500.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL12103', 'perkara' => 'Elaun Keraian', 'jenis' => 'OL', 'jumlah' => 128400.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL12106', 'perkara' => 'Imbuhan Tetap Jawatan Utama dan Gred Khas', 'jenis' => 'OL', 'jumlah' => 24000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL12107', 'perkara' => 'Bayaran Insentif Perkhidmatan Kritikal', 'jenis' => 'OL', 'jumlah' => 49500.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL12108', 'perkara' => 'Bayaran Insentif Khas Pegawai Profesional', 'jenis' => 'OL', 'jumlah' => 0.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL12109', 'perkara' => 'Bayaran Insentif Tugas Kewangan', 'jenis' => 'OL', 'jumlah' => 1400.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL12199', 'perkara' => 'Elaun Tetap Lain', 'jenis' => 'OL', 'jumlah' => 187900.00, 'tahun' => $tahun],

            // --- OS13000: SUMBANGAN BERKANUN ---
            ['kod_objek' => 'OS13000', 'perkara' => 'SUMBANGAN BERKANUN UNTUK KAKITANGAN', 'jenis' => 'OS', 'jumlah' => 10900.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL13101', 'perkara' => 'Sumbangan Berkanun (KWSP)', 'jenis' => 'OL', 'jumlah' => 10900.00, 'tahun' => $tahun],

            // --- OS14000: BAYARAN LEBIH MASA ---
            ['kod_objek' => 'OS14000', 'perkara' => 'BAYARAN LEBIH MASA', 'jenis' => 'OS', 'jumlah' => 26800.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL14101', 'perkara' => 'Bayaran Lebih Masa Kakitangan Awam', 'jenis' => 'OL', 'jumlah' => 26800.00, 'tahun' => $tahun],

            // --- OS15000: FAEDAH KEWANGAN LAIN ---
            ['kod_objek' => 'OS15000', 'perkara' => 'FAEDAH-FAEDAH KEWANGAN YANG LAIN', 'jenis' => 'OS', 'jumlah' => 88100.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL15101', 'perkara' => 'Bayaran dan Bayaran Balik Utiliti', 'jenis' => 'OL', 'jumlah' => 0.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL15102', 'perkara' => 'Bayaran Balik Pasport/Lesen/Yuran Profesional', 'jenis' => 'OL', 'jumlah' => 400.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL15110', 'perkara' => 'Pemberian Alat Komunikasi', 'jenis' => 'OL', 'jumlah' => 0.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL15111', 'perkara' => 'Bayaran Kemudahan Perubatan', 'jenis' => 'OL', 'jumlah' => 48000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL15112', 'perkara' => 'Pelbagai Elaun Pakaian', 'jenis' => 'OL', 'jumlah' => 14500.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL15113', 'perkara' => 'Pemberian Anugerah Perkhidmatan Cemerlang (APC)', 'jenis' => 'OL', 'jumlah' => 0.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL15114', 'perkara' => 'Pelbagai Kemudahan Tambang Pengangkutan', 'jenis' => 'OL', 'jumlah' => 25000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL15119', 'perkara' => 'Faedah Kewangan Lain / Elaun Perkakasan', 'jenis' => 'OL', 'jumlah' => 200.00, 'tahun' => $tahun],

            // =================================================================
            // OA20000: PERKHIDMATAN & BEKALAN
            // =================================================================
            ['kod_objek' => 'OA20000', 'perkara' => 'PERKHIDMATAN & BEKALAN', 'jenis' => 'OA', 'jumlah' => 300100.00, 'tahun' => $tahun],

            // --- OS21000: PERJALANAN & SARA HIDUP ---
            ['kod_objek' => 'OS21000', 'perkara' => 'PERBELANJAAN PERJALANAN & SARA HIDUP', 'jenis' => 'OS', 'jumlah' => 73000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL21101', 'perkara' => 'Makanan & Minuman', 'jenis' => 'OL', 'jumlah' => 5300.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL21102', 'perkara' => 'Penginapan', 'jenis' => 'OL', 'jumlah' => 20500.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL21104', 'perkara' => 'Elaun Perjalanan, Tambang Bas & Teksi', 'jenis' => 'OL', 'jumlah' => 32200.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL21106', 'perkara' => 'Bayaran Kapal Terbang', 'jenis' => 'OL', 'jumlah' => 15000.00, 'tahun' => $tahun],

            // --- OS22000: PENGANGKUTAN BARANG ---
            ['kod_objek' => 'OS22000', 'perkara' => 'PENGANGKUTAN BARANG-BARANG', 'jenis' => 'OS', 'jumlah' => 4800.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL22155', 'perkara' => 'Pengangkutan Barang / Elaun Perpindahan', 'jenis' => 'OL', 'jumlah' => 4800.00, 'tahun' => $tahun],

            // --- OS23000: PERHUBUNGAN & UTILITI ---
            ['kod_objek' => 'OS23000', 'perkara' => 'PERHUBUNGAN DAN UTILITI', 'jenis' => 'OS', 'jumlah' => 20300.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL23101', 'perkara' => 'Pos Biasa, Mel Udara, Berdaftar & Ekspress', 'jenis' => 'OL', 'jumlah' => 7900.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL23102', 'perkara' => 'Telefon, Sewaan & Pemasangan', 'jenis' => 'OL', 'jumlah' => 7600.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL23103', 'perkara' => 'Telex, Telegraf, Kabel, Wireless & Satelit', 'jenis' => 'OL', 'jumlah' => 4800.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL23201', 'perkara' => 'Utiliti - Elektrik', 'jenis' => 'OL', 'jumlah' => 0.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL23202', 'perkara' => 'Utiliti - Air', 'jenis' => 'OL', 'jumlah' => 0.00, 'tahun' => $tahun],

            // --- OS24000: SEWAAN ---
            ['kod_objek' => 'OS24000', 'perkara' => 'SEWAAN', 'jenis' => 'OS', 'jumlah' => 17800.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL24201', 'perkara' => 'Sewa Bangunan Kediaman', 'jenis' => 'OL', 'jumlah' => 0.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL24501', 'perkara' => 'Sewa Alat Kelengkapan Pejabat (cth: Fotostat)', 'jenis' => 'OL', 'jumlah' => 300.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL24699', 'perkara' => 'Sewa Alat Kelengkapan Elektronik Lain', 'jenis' => 'OL', 'jumlah' => 13200.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL24799', 'perkara' => 'Sewa Alat Kelengkapan Elektrik Lain (Pemanas Air)', 'jenis' => 'OL', 'jumlah' => 4300.00, 'tahun' => $tahun],

            // --- OS25000: BAHAN MAKANAN ---
            ['kod_objek' => 'OS25000', 'perkara' => 'BAHAN MAKANAN DAN MINUMAN', 'jenis' => 'OS', 'jumlah' => 0.00, 'tahun' => $tahun],

            // --- OS26000: BAHAN MENTAH & PENYELENGGARAAN ---
            ['kod_objek' => 'OS26000', 'perkara' => 'BEKALAN BAHAN MENTAH & PENYELENGGARAAN', 'jenis' => 'OS', 'jumlah' => 11600.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL26121', 'perkara' => 'Alat Ganti Kelengkapan Pejabat', 'jenis' => 'OL', 'jumlah' => 1400.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL26131', 'perkara' => 'Alat Ganti Kelengkapan Elektrik', 'jenis' => 'OL', 'jumlah' => 1200.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL26201', 'perkara' => 'Minyak Petrol', 'jenis' => 'OL', 'jumlah' => 9000.00, 'tahun' => $tahun],

            // --- OS27000: BEKALAN DAN BAHAN LAIN ---
            ['kod_objek' => 'OS27000', 'perkara' => 'BEKALAN DAN BAHAN LAIN', 'jenis' => 'OS', 'jumlah' => 70500.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27101', 'perkara' => 'Surat Khabar, Majalah, Warta', 'jenis' => 'OL', 'jumlah' => 2700.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27102', 'perkara' => 'Alat Tulis Pejabat', 'jenis' => 'OL', 'jumlah' => 29000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27103', 'perkara' => 'Alat Tulis Komputer', 'jenis' => 'OL', 'jumlah' => 12400.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27199', 'perkara' => 'Bekalan Pejabat Lain', 'jenis' => 'OL', 'jumlah' => 13900.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27299', 'perkara' => 'Bekalan Am Lain', 'jenis' => 'OL', 'jumlah' => 5500.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27401', 'perkara' => 'Ubat dan Dadah', 'jenis' => 'OL', 'jumlah' => 900.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27499', 'perkara' => 'Bekalan Perubatan Lain', 'jenis' => 'OL', 'jumlah' => 800.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27605', 'perkara' => 'Pakaian Seragam', 'jenis' => 'OL', 'jumlah' => 3200.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL27699', 'perkara' => 'Pakaian (Kasut, Jam, Tali Leher)', 'jenis' => 'OL', 'jumlah' => 2100.00, 'tahun' => $tahun],

            // --- OS28000: PENYELENGGARAAN & PEMBAIKAN ---
            ['kod_objek' => 'OS28000', 'perkara' => 'PENYELENGGARAAN & PEMBAIKAN', 'jenis' => 'OS', 'jumlah' => 31000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL28501', 'perkara' => 'Penyelenggaraan Alat Pejabat', 'jenis' => 'OL', 'jumlah' => 10000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL28599', 'perkara' => 'Penyelenggaraan Perabot', 'jenis' => 'OL', 'jumlah' => 1000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL28601', 'perkara' => 'Penyelenggaraan Komputer', 'jenis' => 'OL', 'jumlah' => 10000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL28699', 'perkara' => 'Penyelenggaraan Elektronik Lain', 'jenis' => 'OL', 'jumlah' => 2400.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL28701', 'perkara' => 'Penyelenggaraan Hawa Dingin', 'jenis' => 'OL', 'jumlah' => 6000.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL28799', 'perkara' => 'Penyelenggaraan Elektrik Lain', 'jenis' => 'OL', 'jumlah' => 1300.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL28801', 'perkara' => 'Penyelenggaraan Telefon/Telex', 'jenis' => 'OL', 'jumlah' => 300.00, 'tahun' => $tahun],

            // --- OS29000: PERKHIDMATAN IKTISAS ---
            ['kod_objek' => 'OS29000', 'perkara' => 'PERKHIDMATAN IKTISAS & HOSPITALITI', 'jenis' => 'OS', 'jumlah' => 71100.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL29107', 'perkara' => 'Perkhidmatan Latihan/Pensyarah', 'jenis' => 'OL', 'jumlah' => 600.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL29199', 'perkara' => 'Perkhidmatan Lain (Meter Reading)', 'jenis' => 'OL', 'jumlah' => 37100.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL29201', 'perkara' => 'Penerbitan Kerajaan', 'jenis' => 'OL', 'jumlah' => 25200.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL29202', 'perkara' => 'Pencetakan Borang/Kad', 'jenis' => 'OL', 'jumlah' => 6700.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL29299', 'perkara' => 'Perkhidmatan Percetakan Lain', 'jenis' => 'OL', 'jumlah' => 500.00, 'tahun' => $tahun],
            ['kod_objek' => 'OL29411', 'perkara' => 'Keraian Pejabat (Mesyuarat)', 'jenis' => 'OL', 'jumlah' => 1000.00, 'tahun' => $tahun],
        ];

        // Insert Batch
        foreach ($data as $item) {
            DB::table('dbuses')->insert([
                'kod_objek' => $item['kod_objek'],
                'perkara'   => $item['perkara'],
                'jenis'     => $item['jenis'],
                'jumlah'    => $item['jumlah'],
                'tahun'     => $item['tahun'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}