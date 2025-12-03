<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agensi;

class AgensiSeeder extends Seeder
{
    public function run()
    {
        // --- SENARAI MELAKA ---
        $agensiMelaka = [
            "AKCC (Ayer Keroh Country Club)",
            "Bahagian Teknologi Maklumat Dan Komunikasi (BTMK)", 
            "BADSA (Bhg. Audit Dalam & Siasatan Awam)",
            "BKP, JKMM (Bhg. Khidmat Pengurusan / Pengurusan Aset)",
            "BKSA (Badan Kawal Selia Air)",
            "BPSM, JKMM (Bhg. Pengurusan Sumber Manusia)",
            "CMI (Pejabat Ketua Menteri)",
            "CUCKOO INTERNATIONAL (MAL) BERHAD",
            "DUN / Unit Dewan",
            "FR Fariza Enterprise",
            "Hospital Putra",
            "ITPS Marketing S/B",
            "Invest Melaka Berhad",
            "Jabatan Kebajikan Masyarakat Negeri Melaka (JKM)", 
            "Jabatan Kewangan & Perbendaharaan Negeri Melaka",
            "Jabatan Mufti Melaka",
            "Jabatan Pendakwaan Syariah",
            "Jabatan Pertanian",
            "Jabatan Perkhidmatan Veterinar Negeri Melaka",
            "Jabatan Pengairan dan Saliran Negeri Melaka",
            "JAIM (Jabatan Agama Islam Melaka)",
            "JKR",
            "JPBD (Jab. Perancangan Bandar & Desa)",
            "Kertas Jemaah Pengampunan",
            "KMB (Kumpulan Melaka Berhad)",
            "Kompleks Falak Al-Khawarizmi",
            "LPM (Lembaga Perumahan Melaka)",
            "LTAM (Lembaga Tabung Amanah Melaka)",
            "Melaka International College of Science and Texhnology (MiCoST)",
            "Melaka Bekal Sdn Bhd",
            "Majlis Mesyuarat Kerajaan Negeri Melaka",
            "Mahkamah Syariah Melaka",
            "MAIM (Majlis Agama Islam Melaka)",
            "MCORP",
            "MITC",
            "MITCH (Melaka ICT Holding)",
            "M-WEZ (Melaka Waterfront Economic Zone)",
            "Panorama Melaka",
            "PBT (MBMB)",
            "PBT (MPAG)",
            "PBT (MPJ)",
            "PBT (MPTHJ)",
            "Pejabat T.Y.T",
            "Perbadanan Biokteknologi Melaka",
            "PERTAM (Perbadanan Kemajuan Tanah Adat Melaka)",
            "PERZIM (Perbadanan Muzium Melaka)",
            "PPSPM (Perbadanan Pembangunan Sungai & Pantai Melaka)",
            "PTD A/GAJAH",
            "PTD JASIN",
            "PTD M/TGH",
            "PTG Negeri Melaka",
            "PTHM (Perbadanan Teknologi Hijau Melaka)",
            "PUTARAN SEMASA SDN BHD",
            "SAMB (Syarikat Air Melaka)",
            "Setiausaha Kerajaan Negeri (suk)",
            "TAPEM (Tabung Amanah Pendidikan Melaka)",
            "UKT, JKMM (Unit Kerajaan Tempatan)",
            "UNIMEL / KUIM (Kolej Universiti Islam Melaka)",
            "Unit Integriti",
            "UTC Melaka",
            "UPEN (Unit Perancangan Ekonomi)",
            "Yayasan Melaka",
            "JAWHAR"
        ];

        foreach ($agensiMelaka as $nama) {
            Agensi::firstOrCreate([
                'nama_agensi' => $nama,
                'negeri'      => 'Melaka'
            ]);
        }

        // --- CONTOH NEGERI LAIN (cth: PERSEKUTUAN/JOHOR) ---
        // Boleh tambah sini nanti kalau perlu
        $agensiUmum = [
            "Polis Diraja Malaysia (PDRM)",
            "Jabatan Peguam Negara (Ibu Pejabat)"
        ];

        foreach ($agensiUmum as $nama) {
            Agensi::firstOrCreate([
                'nama_agensi' => $nama,
                'negeri'      => 'Persekutuan'
            ]);
        }
    }
}