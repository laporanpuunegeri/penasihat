@extends('layouts.app')

@section('content')
<style>
    .form-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden; }
    .form-section-title { color: #1e293b; font-weight: 700; font-size: 1rem; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-label { font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; }
    .calc-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
    .calc-table th { text-align: center; background: #f8f9fa; padding: 10px; border: 1px solid #e2e8f0; font-size: 0.75rem; text-transform: uppercase; color: #64748b; }
    .calc-table td { padding: 0 5px; vertical-align: middle; }
    .input-manual { border: 2px solid #3b82f6; background-color: #eff6ff; text-align: center; font-weight: bold; }
    .input-auto { background-color: #f1f5f9; border: 1px solid #cbd5e1; text-align: center; color: #64748b; pointer-events: none; }
    .input-final { background-color: #dcfce7; border: 2px solid #22c55e; text-align: center; font-weight: bold; color: #15803d; }
    .calc-row-title { font-weight: bold; font-size: 0.9rem; color: #1e293b; padding-left: 10px; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark">
                {{ isset($rekod) ? 'Kemaskini' : 'Daftar' }} Prestasi PPUUN Tahun {{ $tahun }}
            </h3>
            <p class="text-muted small mb-0">Pengiraan automatik berdasarkan beban kes dan penyelesaian.</p>
        </div>
        <a href="{{ route('pentadbiran.laporan_prestasi.index', ['tahun' => $tahun]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <form action="{{ route('pentadbiran.laporan_prestasi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                <div class="form-card p-4 mb-4">
                    <h5 class="form-section-title text-primary"><i class="fas fa-bullseye me-2"></i> A. PILIH OUTCOME</h5>
                    <div class="mb-4">
                        <select name="outcome_id" id="outcome_id" class="form-select form-select-lg shadow-sm" 
                                onchange="window.location.href='{{ route('pentadbiran.laporan_prestasi.create') }}?tahun={{ $tahun }}&outcome_id=' + this.value">
                            <option value="">-- Sila Pilih Outcome --</option>
                            @foreach($outcomes as $key => $details)
                                <option value="{{ $key }}" {{ ($selected_outcome == $key) ? 'selected' : '' }}>
                                    {{ $key }} - {{ $details['tajuk'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($selected_outcome)
                        <div class="mt-4">
                            <h5 class="form-section-title text-dark"><i class="fas fa-file-alt me-2"></i> B. Maklumat KPI</h5>
                            <div class="row g-3">
                                <div class="col-md-9">
                                    <label class="form-label">Penerangan KPI</label>
                                    <textarea name="kpi_desc" class="form-control" rows="2" required>{{ old('kpi_desc', $rekod->kpi_desc ?? '') }}</textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sasaran Tahunan (%)</label>
                                    <input type="number" name="sasaran" class="form-control text-center" value="{{ old('sasaran', $rekod->sasaran_tahunan ?? 90) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h5 class="form-section-title text-success"><i class="fas fa-calculator me-2"></i> C. Pengiraan Pencapaian (Auto Carry-Forward)</h5>
                            <div class="table-responsive">
                                <table class="calc-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 15%;">Suku Tahun</th>
                                            <th style="width: 15%;">Baki Terdahulu<br>(Carry Fwd)</th>
                                            <th style="width: 15%;">Kes Masuk<br>(Baru)</th>
                                            <th style="width: 15%;">Jumlah Beban<br>(Baki + Masuk)</th>
                                            <th style="width: 15%;">Kes Selesai</th>
                                            <th style="width: 10%;">Baki Semasa<br>(Belum Selesai)</th>
                                            <th style="width: 15%;">% Pencapaian<br>(Simpan ke DB)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i = 1; $i <= 4; $i++)
                                        <tr>
                                            <td class="calc-row-title">SUKU {{ $i }}</td>
                                            
                                            {{-- BAKI AWAL (Auto) --}}
                                            <td>
                                                <input type="number" id="baki_awal_{{ $i }}" class="form-control input-auto" value="0" readonly>
                                            </td>
                                            
                                            {{-- KES MASUK (Data Lama Diambil Dari Sini) --}}
                                            <td>
                                                <input type="number" name="masuk[]" id="masuk_{{ $i }}" 
                                                       class="form-control input-manual" placeholder="0" 
                                                       value="{{ $rekod->beban_kes['masuk'][$i-1] ?? 0 }}" 
                                                       oninput="kiraStatistik()">
                                            </td>

                                            {{-- BEBAN (Auto) --}}
                                            <td>
                                                <input type="number" id="beban_{{ $i }}" class="form-control input-auto" value="0" readonly>
                                            </td>

                                            {{-- KES SELESAI (Data Lama Diambil Dari Sini) --}}
                                            <td>
                                                <input type="number" name="selesai[]" id="selesai_{{ $i }}" 
                                                       class="form-control input-manual" placeholder="0" 
                                                       value="{{ $rekod->beban_kes['selesai'][$i-1] ?? 0 }}"
                                                       oninput="kiraStatistik()">
                                            </td>

                                            {{-- BAKI AKHIR (Auto) --}}
                                            <td>
                                                <input type="number" id="baki_akhir_{{ $i }}" class="form-control input-auto" value="0" readonly>
                                            </td>

                                            {{-- PERCENTAGE --}}
                                            <td>
                                                <input type="text" name="suku[]" id="peratus_{{ $i }}" 
                                                       class="form-control input-final" 
                                                       value="{{ old('suku.' . ($i-1), $rekod->{'suku_'.$i} ?? 0) }}" readonly>
                                            </td>
                                        </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i> <strong>Cara Guna:</strong> Isi kolum <strong>BIRU</strong> sahaja (Kes Masuk & Kes Selesai). Sistem akan automatik kira peratusan dan bawa baki kes ke suku seterusnya.
                            </div>
                        </div>

                        <div class="mt-5">
                            <h5 class="form-section-title text-info"><i class="fas fa-edit me-2"></i> D. Data Catatan</h5>
                            <div class="p-3 bg-light border rounded">
                                @php
                                    $labels = $outcomes[$selected_outcome]['cat_labels'] ?? [];
                                    $catatanLama = $rekod->catatan_data ?? []; 
                                @endphp
                                @foreach($labels as $label)
                                    <div class="row mb-2 align-items-center border-bottom pb-2">
                                        <div class="col-md-8">
                                            <label class="form-label mb-0">{{ $loop->iteration }}. {{ $label }}</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="catatan_val[]" class="form-control form-control-sm" 
                                                   value="{{ $catatanLama[$label] ?? '' }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-lg shadow px-5">
                                <i class="fas fa-save me-2"></i> Simpan Laporan
                            </button>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-arrow-up fa-3x mb-3 text-gray-300"></i>
                            <p>Sila pilih <strong>Outcome Program</strong> dahulu.</p>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function kiraStatistik() {
    let bakiBawaDepan = 0; 

    for (let i = 1; i <= 4; i++) {
        let kesMasuk = parseFloat(document.getElementById('masuk_' + i).value) || 0;
        let kesSelesai = parseFloat(document.getElementById('selesai_' + i).value) || 0;

        // Set Baki Awal
        document.getElementById('baki_awal_' + i).value = bakiBawaDepan;

        // Kira Jumlah Beban
        let jumlahBeban = bakiBawaDepan + kesMasuk;
        document.getElementById('beban_' + i).value = jumlahBeban;

        // Kira Peratusan
        let peratus = 0;
        if (jumlahBeban > 0) {
            peratus = (kesSelesai / jumlahBeban) * 100;
        }
        
        let finalPercent = Math.min(Math.round(peratus), 100);
        document.getElementById('peratus_' + i).value = finalPercent;

        // Kira Baki Akhir
        let bakiAkhir = Math.max(0, jumlahBeban - kesSelesai);
        document.getElementById('baki_akhir_' + i).value = bakiAkhir;

        // Pass baki ke depan
        bakiBawaDepan = bakiAkhir;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    kiraStatistik();
});
</script>
@endsection