@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Butiran Gaji & Upah (OS11000) - Tahun {{ $tahun }}</h3>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun'=>$tahun]) }}" class="btn btn-secondary shadow-sm">Kembali</a>
    </div>
    
    <form action="{{ route('pentadbiran.dbus.pecahan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    {{-- HEADER JADUAL --}}
                    <table class="table table-bordered table-sm mb-0 text-center align-middle" style="font-size: 0.8rem;">
                        <thead style="background-color: #212529; color: white;">
                            <tr>
                                <th rowspan="2" style="width: 3%">Bil</th>
                                <th rowspan="2" style="width: 20%">Nama Pegawai</th>
                                <th rowspan="2" style="width: 7%">Gred</th>
                                <th style="background-color: #444;">PERUNTUKAN GAJI SEMASA<br><small>(Tahun {{ $tahun - 1 }})</small></th>
                                <th style="background-color: #444;">ANGGARAN PERUNTUKAN GAJI<br><small>(Tahun {{ $tahun }})</small></th>
                                <th rowspan="2" style="width: 10%">JUMLAH KESELURUHAN (RM)</th>
                                <th rowspan="2" style="width: 8%">3/7% KENAIKAN (RM)</th>
                                <th rowspan="2" style="width: 8%">KENAIKAN GAJI (RM)</th>
                                <th rowspan="2" style="width: 12%">KGT BULAN (Pergerakan)</th>
                                <th rowspan="2" style="width: 5%">Aksi</th>
                            </tr>
                            <tr>
                                <th class="text-warning">RM</th>
                                <th class="text-success">RM</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-pegawai">
                            @foreach ($pegawai as $idx => $p)
                            <tr class="row-data">
                                <td>{{ $idx + 1 }}</td>
                                <td><input type="text" name="pegawai[{{$idx}}][nama]" class="form-control form-control-sm border-0" value="{{ $p->nama_pegawai }}"></td>
                                <td><input type="text" name="pegawai[{{$idx}}][gred]" class="form-control form-control-sm border-0 text-center input-gred" value="{{ $p->gred }}"></td>
                                
                                {{-- Gaji Semasa --}}
                                <td><input type="number" step="0.01" name="pegawai[{{$idx}}][gaji_2025]" class="form-control form-control-sm text-end input-gaji-semasa" value="{{ $p->gaji_2025 }}"></td>
                                
                                {{-- Anggaran Gaji (Gaji Baru) --}}
                                <td><input type="number" step="0.01" name="pegawai[{{$idx}}][gaji_2026]" class="form-control form-control-sm text-end input-gaji-anggaran bg-light" value="{{ $p->gaji_2026 }}" readonly></td>
                                
                                {{-- Jumlah Keseluruhan --}}
                                <td class="bg-light"><input type="number" step="0.01" name="pegawai[{{$idx}}][jumlah_keseluruhan]" class="form-control form-control-sm text-end fw-bold bg-transparent border-0 input-total" value="{{ $p->jumlah_keseluruhan }}" readonly></td>
                                
                                {{-- 3/7% Kenaikan --}}
                                <td><input type="number" step="0.01" name="pegawai[{{$idx}}][kenaikan_peratus]" class="form-control form-control-sm text-end input-kenaikan-peratus" value="{{ $p->kenaikan_peratus }}" readonly></td>
                                
                                {{-- Kenaikan Gaji (beza) --}}
                                <td><input type="number" step="0.01" name="pegawai[{{$idx}}][kenaikan_gaji]" class="form-control form-control-sm text-end input-kenaikan-gaji" value="{{ $p->kenaikan_gaji }}" readonly></td>
                                
                                {{-- KGT Bulan --}}
                                <td>
                                    <select name="pegawai[{{$idx}}][bulan]" class="form-select form-select-sm input-kgt-bulan fw-bold text-primary">
                                        <option value="JANUARI" {{ $p->bulan_pergerakan == 'JANUARI' ? 'selected' : '' }}>JANUARI</option>
                                        <option value="APRIL" {{ $p->bulan_pergerakan == 'APRIL' ? 'selected' : '' }}>APRIL</option>
                                        <option value="JULAI" {{ $p->bulan_pergerakan == 'JULAI' ? 'selected' : '' }}>JULAI</option>
                                        <option value="OKTOBER" {{ $p->bulan_pergerakan == 'OKTOBER' ? 'selected' : '' }}>OKTOBER</option>
                                    </select>
                                </td>
                                
                                <input type="hidden" name="pegawai[{{$idx}}][id]" value="{{ $p->id }}">
                                <td><button type="button" class="btn btn-danger btn-sm px-2 remove-row"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @endforeach

                            {{-- Row Kosong jika tiada data --}}
                            @if($pegawai->isEmpty())
                            <tr class="row-data">
                                <td>1</td>
                                <td><input type="text" name="pegawai[0][nama]" class="form-control form-control-sm border-0" placeholder="Nama"></td>
                                <td><input type="text" name="pegawai[0][gred]" class="form-control form-control-sm border-0 text-center input-gred" placeholder="Gred"></td>
                                <td><input type="number" step="0.01" name="pegawai[0][gaji_2025]" class="form-control form-control-sm text-end input-gaji-semasa" placeholder="0.00"></td>
                                <td><input type="number" step="0.01" name="pegawai[0][gaji_2026]" class="form-control form-control-sm text-end input-gaji-anggaran bg-light" readonly></td>
                                <td class="bg-light"><input type="number" step="0.01" name="pegawai[0][jumlah_keseluruhan]" class="form-control form-control-sm text-end fw-bold bg-transparent border-0 input-total" readonly></td>
                                <td><input type="number" step="0.01" name="pegawai[0][kenaikan_peratus]" class="form-control form-control-sm text-end input-kenaikan-peratus" readonly></td>
                                <td><input type="number" step="0.01" name="pegawai[0][kenaikan_gaji]" class="form-control form-control-sm text-end input-kenaikan-gaji" readonly></td>
                                <td>
                                    <select name="pegawai[0][bulan]" class="form-select form-select-sm input-kgt-bulan fw-bold text-primary">
                                        <option value="JANUARI">JANUARI</option>
                                        <option value="APRIL">APRIL</option>
                                        <option value="JULAI">JULAI</option>
                                        <option value="OKTOBER">OKTOBER</option>
                                    </select>
                                </td>
                                <input type="hidden" name="pegawai[0][id]" value="">
                                <td></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="button" class="btn btn-success btn-sm" id="add-row-btn"><i class="fas fa-plus"></i> Tambah Pegawai</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan & Kira Total</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 🔥 FUNGSI KIRAAN AUTOMATIK (LOGIK DIPERBETULKAN)
        function calculateRow(row) {
            // 1. Ambil Inputs
            const gajiSemasaInput = row.querySelector('.input-gaji-semasa');
            const gredInput = row.querySelector('.input-gred');
            const bulanSelect = row.querySelector('.input-kgt-bulan');

            // 2. Ambil Nilai
            let gajiSemasa = parseFloat(gajiSemasaInput.value) || 0;
            let gred = gredInput.value.toLowerCase();
            let bulan = bulanSelect.value;

            // 3. Tentukan Peratus (3% untuk JUSA/VU, 7% Lain-lain)
            let peratus = 0.07; // Default 7%
            if (gred.includes('jusa') || gred.includes('vu')) {
                peratus = 0.03; // 3%
            }

            // 4. KIRA: Anggaran Gaji Baru (Sebulan)
            // Formula: (Gaji Semasa * Peratus) + Gaji Semasa
            let gajiBaru = (gajiSemasa * peratus) + gajiSemasa;

            // 5. KIRA: 3/7% Kenaikan (Implikasi Setahun)
            // Formula: Gaji Semasa * Peratus * 12
            let kenaikanSetahun = gajiSemasa * peratus * 12;

            // 🔥 6. KIRA: Kenaikan Gaji (Perbezaan Sebulan)
            // Formula: Gaji Baru - Gaji Lama
            let bezaGaji = gajiBaru - gajiSemasa;

            // 7. KIRA: Jumlah Keseluruhan (Ikut Bulan KGT)
            let jumlahKeseluruhan = 0;
            
            if (bulan === 'JANUARI') {
                // Gaji Baru x 12
                jumlahKeseluruhan = gajiBaru * 12;
            } 
            else if (bulan === 'APRIL') {
                // (Gaji Lama x 3) + (Gaji Baru x 9)
                jumlahKeseluruhan = (gajiSemasa * 3) + (gajiBaru * 9);
            } 
            else if (bulan === 'JULAI') {
                // (Gaji Lama x 6) + (Gaji Baru x 6)
                jumlahKeseluruhan = (gajiSemasa * 6) + (gajiBaru * 6);
            } 
            else if (bulan === 'OKTOBER') {
                // (Gaji Lama x 9) + (Gaji Baru x 3)
                jumlahKeseluruhan = (gajiSemasa * 9) + (gajiBaru * 3);
            }

            // 8. UPDATE FIELD DI SKRIN
            row.querySelector('.input-gaji-anggaran').value = gajiBaru.toFixed(2);
            row.querySelector('.input-kenaikan-peratus').value = kenaikanSetahun.toFixed(2);
            
            // 🔥 Papar beza gaji (contoh: 485.64)
            row.querySelector('.input-kenaikan-gaji').value = bezaGaji.toFixed(2); 
            
            row.querySelector('.input-total').value = jumlahKeseluruhan.toFixed(2);
        }

        // --- EVENT LISTENER ---
        function attachEvents() {
            document.querySelectorAll('.row-data').forEach(row => {
                const inputs = row.querySelectorAll('.input-gaji-semasa, .input-gred, .input-kgt-bulan');
                inputs.forEach(input => {
                    input.removeEventListener('input', handleInput); 
                    input.removeEventListener('change', handleInput);
                    input.addEventListener('input', handleInput);
                    input.addEventListener('change', handleInput);
                });
            });
        }

        function handleInput(e) {
            const row = e.target.closest('tr');
            calculateRow(row);
        }

        // Init
        attachEvents();
        document.querySelectorAll('.row-data').forEach(row => calculateRow(row));

        // Tambah Baris
        document.getElementById('add-row-btn').addEventListener('click', function() {
            const tbody = document.getElementById('tbody-pegawai');
            const rows = tbody.querySelectorAll('tr.row-data');
            const newIndex = rows.length;
            
            const clone = rows[0].cloneNode(true);
            
            clone.querySelectorAll('input').forEach(input => input.value = '');
            clone.querySelector('.input-kgt-bulan').value = 'JANUARI'; 
            clone.querySelector('td').innerText = newIndex + 1; 
            
            clone.querySelectorAll('input, select, textarea').forEach(input => {
                let name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
                }
            });

            tbody.appendChild(clone);
            attachEvents(); 
        });

        // Buang Baris
        document.getElementById('tbody-pegawai').addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                const row = e.target.closest('tr');
                if (document.querySelectorAll('.row-data').length > 1) {
                    row.remove();
                    document.querySelectorAll('.row-data').forEach((r, i) => {
                        r.querySelector('td').innerText = i + 1;
                    });
                } else {
                    alert("Sekurang-kurangnya satu baris diperlukan.");
                }
            }
        });
    });
</script>
@endpush
@endsection