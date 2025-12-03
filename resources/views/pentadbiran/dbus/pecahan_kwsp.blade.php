@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Sumbangan Berkanun (OS13000) - Tahun {{ $tahun }}</h3>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun'=>$tahun]) }}" class="btn btn-secondary shadow-sm">Kembali</a>
    </div>
    
    <div class="alert alert-info border-0 shadow-sm">
        <i class="fas fa-info-circle me-2"></i> 
        Sila **TANDAKAN** kotak "Kecuali" bagi pegawai yang tidak layak caruman KWSP (berpencen). Baris tersebut akan disifarkan (zeroed out) dalam pengiraan total.
    </div>
    
    <form action="{{ route('pentadbiran.dbus.pecahan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahun }}">
        <input type="hidden" name="kod_transaksi" value="OS13000">

        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle text-center" style="font-size: 0.8rem;">
                        <thead class="table-dark">
                            <tr>
                                <th rowspan="2" style="width: 3%">Bil</th>
                                <th rowspan="2" style="width: 20%">Nama Pegawai</th>
                                <th rowspan="2" style="width: 5%">Gred</th>
                                
                                <th colspan="3" class="bg-secondary">GAJI SEKARANG / SEMASA</th>
                                <th colspan="3" style="background-color: #444;">GAJI SELEPAS PELARASAN</th>
                                
                                <th rowspan="2" style="width: 12%">JUMLAH SUMBANGAN<br>MAJIKAN SETAHUN (RM)</th>
                                <th rowspan="2" style="width: 10%">CATATAN</th>
                                <th rowspan="2" style="width: 5%">PADAM</th> {{-- Button Padam Baris --}}
                                <th rowspan="2" style="width: 5%">KECUALI</th> {{-- 🔥 Checkbox Kecuali --}}
                            </tr>
                            <tr>
                                <th style="width: 10%" class="bg-light text-dark">GAJI + ELAUN (RM)</th>
                                <th style="width: 5%" class="bg-light text-dark">% SUMBANGAN</th>
                                <th style="width: 5%" class="bg-light text-dark">BULAN</th>
                                
                                <th style="width: 10%" class="bg-light text-dark">GAJI + ELAUN (RM)</th>
                                <th style="width: 5%" class="bg-light text-dark">% SUMBANGAN</th>
                                <th style="width: 5%" class="bg-light text-dark">BULAN</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-pegawai">
                            @foreach ($pegawai as $idx => $p)
                            <tr class="row-data">
                                <td>{{ $idx + 1 }}</td>
                                <td><input type="text" name="pegawai[{{$idx}}][nama]" class="form-control form-control-sm border-0" value="{{ $p->nama_pegawai }}" readonly></td>
                                <td><input type="text" name="pegawai[{{$idx}}][gred]" class="form-control form-control-sm border-0 text-center" value="{{ $p->gred }}" readonly></td>
                                
                                {{-- FASA 1 --}}
                                <td>
                                    <input type="number" step="0.01" name="pegawai[{{$idx}}][kwsp_gaji_semasa]" class="form-control form-control-sm text-end calc-item input-gaji-1" 
                                           value="{{ $p->kwsp_gaji_semasa > 0 ? $p->kwsp_gaji_semasa : $p->auto_gaji_elaun_semasa }}">
                                </td>
                                <td><input type="number" name="pegawai[{{$idx}}][kwsp_peratus_semasa]" class="form-control form-control-sm text-center calc-item input-peratus-1" value="{{ $p->kwsp_peratus_semasa ?: 11 }}"></td>
                                <td><input type="number" name="pegawai[{{$idx}}][kwsp_bulan_semasa]" class="form-control form-control-sm text-center calc-item input-bulan-1" value="{{ $p->kwsp_bulan_semasa }}"></td>
                                
                                {{-- FASA 2 --}}
                                <td>
                                    <input type="number" step="0.01" name="pegawai[{{$idx}}][kwsp_gaji_baru]" class="form-control form-control-sm text-end calc-item input-gaji-2" 
                                           value="{{ $p->kwsp_gaji_baru > 0 ? $p->kwsp_gaji_baru : $p->auto_gaji_elaun_baru }}">
                                </td>
                                <td><input type="number" name="pegawai[{{$idx}}][kwsp_peratus_baru]" class="form-control form-control-sm text-center calc-item input-peratus-2" value="{{ $p->kwsp_peratus_baru ?: 11 }}"></td>
                                <td><input type="number" name="pegawai[{{$idx}}][kwsp_bulan_baru]" class="form-control form-control-sm text-center calc-item input-bulan-2 bg-light fw-bold" value="{{ $p->kwsp_bulan_baru }}" readonly></td>

                                {{-- TOTAL --}}
                                <td class="bg-light">
                                    <input type="text" name="pegawai[{{$idx}}][kwsp_total]" class="form-control form-control-sm text-end fw-bold border-0 bg-transparent input-total text-primary" value="{{ number_format($p->kwsp_total, 2) }}" readonly>
                                </td>
                                
                                {{-- CATATAN --}}
                                <td>
                                    <textarea name="pegawai[{{$idx}}][catatan]" class="form-control form-control-sm border-0 text-center" rows="2">{{ $p->catatan ?: ($p->bulan_pergerakan ? 'Pergerakan Gaji Bulan '.$p->bulan_pergerakan : '') }}</textarea>
                                </td>

                                {{-- PADAM BARIS --}}
                                <td class="align-middle">
                                    <button type="button" class="btn btn-danger btn-sm px-2 remove-row" title="Padam Baris">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>

                                {{-- 🔥 KECUALI CHECKBOX --}}
                                <td class="align-middle">
                                    <input type="checkbox" 
                                           name="pegawai[{{$idx}}][is_excluded]" 
                                           class="form-check-input check-exclude" 
                                           value="true" 
                                           {{ $p->is_excluded ? 'checked' : '' }}>
                                </td>

                                <input type="hidden" name="pegawai[{{$idx}}][id]" value="{{ $p->id }}">
                            </tr>
                            @endforeach
                            
                            {{-- Empty Row (Default) --}}
                            @if($pegawai->isEmpty())
                             <tr class="row-data">
                                <td>1</td>
                                <td><input type="text" name="pegawai[0][nama]" class="form-control form-control-sm border-0"></td>
                                <td><input type="text" name="pegawai[0][gred]" class="form-control form-control-sm border-0 text-center"></td>
                                <td><input type="number" step="0.01" name="pegawai[0][kwsp_gaji_semasa]" class="form-control form-control-sm text-end calc-item input-gaji-1"></td>
                                <td><input type="number" name="pegawai[0][kwsp_peratus_semasa]" class="form-control form-control-sm text-center calc-item input-peratus-1" value="11"></td>
                                <td><input type="number" name="pegawai[0][kwsp_bulan_semasa]" class="form-control form-control-sm text-center calc-item input-bulan-1"></td>
                                <td><input type="number" step="0.01" name="pegawai[0][kwsp_gaji_baru]" class="form-control form-control-sm text-end calc-item input-gaji-2"></td>
                                <td><input type="number" name="pegawai[0][kwsp_peratus_baru]" class="form-control form-control-sm text-center calc-item input-peratus-2" value="11"></td>
                                <td><input type="number" name="pegawai[0][kwsp_bulan_baru]" class="form-control form-control-sm text-center calc-item input-bulan-2 bg-light fw-bold" readonly></td>
                                <td class="bg-light"><input type="text" name="pegawai[0][kwsp_total]" class="form-control form-control-sm text-end fw-bold border-0 bg-transparent input-total" readonly></td>
                                <td><textarea name="pegawai[0][catatan]" class="form-control form-control-sm border-0 text-center" rows="2"></textarea></td>
                                <input type="hidden" name="pegawai[0][id]" value="">
                                <td><button type="button" class="btn btn-danger btn-sm px-2 remove-row"><i class="fas fa-trash"></i></button></td>
                                <td><input type="checkbox" name="pegawai[0][is_excluded]" class="form-check-input check-exclude" value="true"></td>
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
        
        function formatNumber(num) { return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); }
        function parseFormattedNumber(str) { return parseFloat(str.toString().replace(/,/g, '')) || 0; }

        function calculateRow(row) {
            const gaji1 = parseFloat(row.querySelector('input[name*="[kwsp_gaji_semasa]"]').value) || 0;
            const peratus1 = parseFloat(row.querySelector('input[name*="[kwsp_peratus_semasa]"]').value) || 0;
            const bulan1Input = row.querySelector('input[name*="[kwsp_bulan_semasa]"]');
            let bulan1 = parseFloat(bulan1Input.value) || 0;

            if (bulan1 > 12) { bulan1 = 12; bulan1Input.value = 12; }
            if (bulan1 < 0) { bulan1 = 0; bulan1Input.value = 0; }
            const bulan2 = 12 - bulan1;
            row.querySelector('input[name*="[kwsp_bulan_baru]"]').value = bulan2;

            const gaji2 = parseFloat(row.querySelector('input[name*="[kwsp_gaji_baru]"]').value) || 0;
            const peratus2 = parseFloat(row.querySelector('input[name*="[kwsp_peratus_baru]"]').value) || 0;

            const sum1 = (gaji1 * (peratus1 / 100)) * bulan1;
            const sum2 = (gaji2 * (peratus2 / 100)) * bulan2;
            
            const total = sum1 + sum2;
            
            const isExcluded = row.querySelector('.check-exclude').checked;

            if (isExcluded) {
                row.querySelector('.input-total').value = "0.00";
                row.style.backgroundColor = '#fce3e3'; 
            } else {
                row.querySelector('.input-total').value = formatNumber(total);
                row.style.backgroundColor = '';
            }
        }

        // Setup Event Listeners
        function attachEvents() {
            document.querySelectorAll('.row-data').forEach(row => {
                row.querySelectorAll('.calc-item, .check-exclude').forEach(input => {
                    input.addEventListener('input', () => calculateRow(row));
                    input.addEventListener('change', () => calculateRow(row));
                });
                calculateRow(row);
            });
        }

        attachEvents();

        // Clone Logic (Standard)
        document.getElementById('add-row-btn').addEventListener('click', function() {
            const tbody = document.getElementById('tbody-pegawai');
            const rows = tbody.querySelectorAll('tr.row-data');
            
            if(rows.length > 0){
                const clone = rows[0].cloneNode(true);
                const newIndex = rows.length;
                clone.querySelector('td').innerText = newIndex + 1;
                clone.style.backgroundColor = '';
                
                clone.querySelectorAll('input').forEach(input => input.value = '');
                clone.querySelector('.check-exclude').checked = false; 

                clone.querySelectorAll('input[name*="peratus"]').forEach(i => i.value = 11);
                clone.querySelector('input[name*="[kwsp_bulan_semasa]"]').value = 0;
                clone.querySelector('input[name*="[kwsp_bulan_baru]"]').value = 12;

                clone.querySelectorAll('input, textarea, select').forEach(input => {
                     let name = input.getAttribute('name');
                     if (name) input.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
                });
                tbody.appendChild(clone);
                attachEvents();
                calculateRow(clone);
            }
        });

        // Remove Row Logic (Padam)
        document.getElementById('tbody-pegawai').addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if(confirm("Adakah anda pasti mahu memadam rekod ini?")) {
                    const row = e.target.closest('tr');
                    // Gantikan logic delete di sini jika perlu hantar ke backend
                    row.remove();
                    // Update row numbers
                    document.querySelectorAll('.row-data').forEach((r, i) => {
                        r.querySelector('td').innerText = i + 1;
                    });
                }
            }
        });
        
        // Clean formatting before submit: Hantar nilai 0 jika dikecualikan
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('.row-data').forEach(row => {
                const isExcludedCheckbox = row.querySelector('.check-exclude');
                const totalInput = row.querySelector('.input-total');

                if (isExcludedCheckbox.checked) {
                    // Jika dikecualikan, paksa nilai total menjadi 0 untuk disimpan dalam DB
                    totalInput.value = 0;
                } else {
                    // Jika tidak dikecualikan, buang koma sebelum dihantar
                    totalInput.value = parseFormattedNumber(totalInput.value);
                }
            });
        });
    });
</script>
@endpush
@endsection