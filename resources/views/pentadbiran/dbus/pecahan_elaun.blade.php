@extends('layouts.app')

@section('content')
<style>
    .input-elaun { font-size: 0.75rem; padding: 2px 5px; height: 25px; text-align: right; }
    .label-elaun { font-size: 0.65rem; font-weight: bold; color: #333; margin-bottom: 0; }
    .td-elaun { vertical-align: top; padding: 5px !important; min-width: 380px; }
    .total-wrapper { display: flex; justify-content: space-between; align-items: center; padding: 0 5px; }
    .currency-label { font-style: italic; font-weight: bold; font-size: 0.85rem; margin-right: 5px; }
    .total-input { border: none; background: transparent !important; font-weight: 800; font-size: 0.95rem; text-align: right; width: 100%; padding: 0; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Butiran Elaun & Imbuhan Tetap (OS12000) - Tahun {{ $tahun }}</h3>
        <a href="{{ route('pentadbiran.dbus.index', ['tahun'=>$tahun]) }}" class="btn btn-secondary shadow-sm">Kembali</a>
    </div>
    
    <form action="{{ route('pentadbiran.dbus.pecahan.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahun }}">
        <input type="hidden" name="kod_transaksi" value="OS12000">

        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle" style="font-size: 0.8rem;">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 3%">Bil</th>
                                <th style="width: 15%">Nama Pegawai</th>
                                <th style="width: 5%">Gred</th>
                                <th style="width: 45%">ELAUN TETAP BULANAN (RM)</th>
                                <th style="width: 10%">JUMLAH SEBULAN</th>
                                <th style="width: 10%">JUMLAH SETAHUN</th>
                                <th style="width: 10%">CATATAN</th>
                                <th style="width: 2%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-pegawai">
                            @foreach ($pegawai as $idx => $p)
                            <tr class="row-data">
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td><input type="text" name="pegawai[{{$idx}}][nama]" class="form-control form-control-sm border-0" value="{{ $p->nama_pegawai }}" placeholder="Nama"></td>
                                <td><input type="text" name="pegawai[{{$idx}}][gred]" class="form-control form-control-sm border-0 text-center" value="{{ $p->gred }}" placeholder="Gred"></td>
                                
                                {{-- KOLUM ELAUN (GRID 2 COLUMN) --}}
                                <td class="td-elaun bg-white">
                                    <div class="row g-1">
                                        {{-- KIRI --}}
                                        <div class="col-6 pe-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="label-elaun">12101 ITKA</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][itka]" class="form-control input-elaun w-50 calc-item" value="{{ $p->itka }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="label-elaun">12102 ITP/EPW</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][itp]" class="form-control input-elaun w-50 calc-item" value="{{ $p->itp }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="label-elaun">12103 ITK</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][el_keraian]" class="form-control input-elaun w-50 calc-item" value="{{ $p->el_keraian }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="label-elaun">12106 ITJU</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][itju]" class="form-control input-elaun w-50 calc-item" value="{{ $p->itju }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="label-elaun">12107 BIPK/EPK</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][bipk]" class="form-control input-elaun w-50 calc-item" value="{{ $p->bipk }}">
                                            </div>
                                        </div>
                                        
                                        {{-- KANAN --}}
                                        <div class="col-6 border-start ps-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="label-elaun">12108 BIKPPK</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][bikppk]" class="form-control input-elaun w-50 calc-item" value="{{ $p->bikppk }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="label-elaun">12109 BITK</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][bitk]" class="form-control input-elaun w-50 calc-item" value="{{ $p->bitk }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="label-elaun">12199 COLA/BSH</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][bsh]" class="form-control input-elaun w-50 calc-item" value="{{ $p->bsh }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="label-elaun">12199 BIW</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][biw]" class="form-control input-elaun w-50 calc-item" value="{{ $p->biw }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="label-elaun">12199 Lain²</label>
                                                <input type="number" step="0.01" name="pegawai[{{$idx}}][el_lain]" class="form-control input-elaun w-50 calc-item" value="{{ $p->el_lain }}">
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- TOTAL SEBULAN --}}
                                <td class="align-middle bg-white">
                                    <div class="total-wrapper">
                                        <span class="currency-label">RM</span>
                                        <input type="text" name="pegawai[{{$idx}}][jumlah_elaun_sebulan]" 
                                            class="total-input input-sebulan" 
                                            value="{{ number_format($p->jumlah_elaun_sebulan, 2, '.', ',') }}" readonly>
                                    </div>
                                </td>
                                
                                {{-- TOTAL SETAHUN --}}
                                <td class="align-middle bg-white">
                                    <div class="total-wrapper">
                                        <span class="currency-label">RM</span>
                                        <input type="text" name="pegawai[{{$idx}}][jumlah_elaun_setahun]" 
                                            class="total-input input-setahun text-primary" 
                                            value="{{ number_format($p->jumlah_elaun_setahun, 2, '.', ',') }}" readonly>
                                    </div>
                                </td>

                                <td class="align-middle p-0">
                                    <textarea name="pegawai[{{$idx}}][catatan]" class="form-control border-0 text-center" rows="4" style="font-size: 0.75rem; resize: none;">{{ $p->catatan }}</textarea>
                                </td>
                                
                                <input type="hidden" name="pegawai[{{$idx}}][id]" value="{{ $p->id }}">
                                <td class="align-middle"><button type="button" class="btn btn-danger btn-sm px-2 remove-row"><i class="fas fa-trash"></i></button></td>
                            </tr>
                            @endforeach
                            
                            @if($pegawai->isEmpty())
                            <tr class="row-data">
                                <td>1</td>
                                <td><input type="text" name="pegawai[0][nama]" class="form-control form-control-sm border-0" placeholder="Nama"></td>
                                <td><input type="text" name="pegawai[0][gred]" class="form-control form-control-sm border-0 text-center" placeholder="Gred"></td>
                                
                                <td class="td-elaun bg-white">
                                    <div class="row g-1">
                                        <div class="col-6 pe-2">
                                            <div class="d-flex justify-content-between mb-1"><label class="label-elaun">12101 ITKA</label><input type="number" step="0.01" name="pegawai[0][itka]" class="form-control input-elaun w-50 calc-item"></div>
                                            <div class="d-flex justify-content-between mb-1"><label class="label-elaun">12102 ITP</label><input type="number" step="0.01" name="pegawai[0][itp]" class="form-control input-elaun w-50 calc-item"></div>
                                            <div class="d-flex justify-content-between mb-1"><label class="label-elaun">12103 ITK</label><input type="number" step="0.01" name="pegawai[0][el_keraian]" class="form-control input-elaun w-50 calc-item"></div>
                                            <div class="d-flex justify-content-between mb-1"><label class="label-elaun">12106 ITJU</label><input type="number" step="0.01" name="pegawai[0][itju]" class="form-control input-elaun w-50 calc-item"></div>
                                            <div class="d-flex justify-content-between"><label class="label-elaun">12107 BIPK</label><input type="number" step="0.01" name="pegawai[0][bipk]" class="form-control input-elaun w-50 calc-item"></div>
                                        </div>
                                        <div class="col-6 border-start ps-2">
                                            <div class="d-flex justify-content-between mb-1"><label class="label-elaun">12108 BIKPPK</label><input type="number" step="0.01" name="pegawai[0][bikppk]" class="form-control input-elaun w-50 calc-item"></div>
                                            <div class="d-flex justify-content-between mb-1"><label class="label-elaun">12109 BITK</label><input type="number" step="0.01" name="pegawai[0][bitk]" class="form-control input-elaun w-50 calc-item"></div>
                                            <div class="d-flex justify-content-between mb-1"><label class="label-elaun">12199 COLA</label><input type="number" step="0.01" name="pegawai[0][bsh]" class="form-control input-elaun w-50 calc-item"></div>
                                            <div class="d-flex justify-content-between mb-1"><label class="label-elaun">12199 BIW</label><input type="number" step="0.01" name="pegawai[0][biw]" class="form-control input-elaun w-50 calc-item"></div>
                                            <div class="d-flex justify-content-between"><label class="label-elaun">12199 Lain²</label><input type="number" step="0.01" name="pegawai[0][el_lain]" class="form-control input-elaun w-50 calc-item"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle bg-white"><div class="total-wrapper"><span class="currency-label">RM</span><input type="text" name="pegawai[0][jumlah_elaun_sebulan]" class="total-input input-sebulan" readonly></div></td>
                                <td class="align-middle bg-white"><div class="total-wrapper"><span class="currency-label">RM</span><input type="text" name="pegawai[0][jumlah_elaun_setahun]" class="total-input input-setahun text-primary" readonly></div></td>
                                <td class="align-middle p-0"><textarea name="pegawai[0][catatan]" class="form-control border-0 text-center" rows="4"></textarea></td>
                                <input type="hidden" name="pegawai[0][id]" value="">
                                <td class="align-middle"><button type="button" class="btn btn-danger btn-sm px-2 remove-row"><i class="fas fa-trash"></i></button></td>
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
        
        function formatNumber(num) {
            return num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function parseFormattedNumber(str) {
            if (!str) return 0;
            return parseFloat(str.toString().replace(/,/g, '')) || 0;
        }

        function calculateRow(row) {
            let totalSebulan = 0;
            row.querySelectorAll('.calc-item').forEach(input => {
                totalSebulan += parseFloat(input.value) || 0;
            });
            const totalSetahun = totalSebulan * 12;

            row.querySelector('.input-sebulan').value = formatNumber(totalSebulan);
            row.querySelector('.input-setahun').value = formatNumber(totalSetahun);
        }

        function attachEvents() {
            document.querySelectorAll('.row-data').forEach(row => {
                row.querySelectorAll('.calc-item').forEach(input => {
                    input.removeEventListener('input', handleInput); 
                    input.addEventListener('input', handleInput);
                });
            });
        }

        function handleInput(e) { calculateRow(e.target.closest('tr')); }

        attachEvents();
        
        document.getElementById('add-row-btn').addEventListener('click', function() {
            const tbody = document.getElementById('tbody-pegawai');
            const rows = tbody.querySelectorAll('tr.row-data');
            
            if(rows.length > 0){
                const clone = rows[0].cloneNode(true);
                const newIndex = rows.length;
                
                clone.querySelector('td').innerText = newIndex + 1;
                clone.querySelectorAll('input').forEach(i => i.value = '');
                clone.querySelector('textarea').value = '';
                
                // Reset total display
                clone.querySelector('.input-sebulan').value = "0.00";
                clone.querySelector('.input-setahun').value = "0.00";

                // Update array index name="pegawai[0][itka]" -> pegawai[1][itka]
                clone.querySelectorAll('input, textarea').forEach(input => {
                     let name = input.getAttribute('name');
                     if (name) input.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
                });
                
                tbody.appendChild(clone);
                attachEvents();
            }
        });
        
        // Clean formatting before submit
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('.total-input').forEach(input => {
                input.value = parseFormattedNumber(input.value);
            });
        });
    });
</script>
@endpush
@endsection