@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">D'BUS (OBB) - Anggaran Belanja</h3>
            <p class="text-muted small">BAHAGIAN/PUUN : PENASIHAT UNDANG-UNDANG NEGERI MELAKA</p>
        </div>
    </div>

    {{-- FILTER TAHUN --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <label class="fw-bold text-secondary me-3 mb-0">TAHUN ANGGARAN:</label>
                <form action="{{ route('pentadbiran.dbus.index') }}" method="GET">
                    <select name="tahun" class="form-select form-select-sm fw-bold border-primary text-primary" style="width: 150px;" onchange="this.form.submit()">
                        @for ($y = 2027; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div class="text-muted small">
                Jumlah Keseluruhan: <span class="fw-bold text-success">RM {{ number_format($grandTotal, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- JADUAL UTAMA --}}
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size: 0.9rem;">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="width: 50%;">BUTIRAN PERBELANJAAN</th>
                            <th style="width: 15%;">OL (Lanjut)</th>
                            <th style="width: 15%;">OS (Sebagai)</th>
                            <th style="width: 15%;">OA (Am)</th>
                            <th style="width: 5%;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($structure as $oaKey => $oa)
                            {{-- BARIS OA (INDUK) --}}
                            <tr class="table-secondary border-bottom-2 border-dark">
                                <td class="fw-bold text-dark">{{ $oaKey }} {{ $oa['perkara'] }}</td>
                                <td></td>
                                <td></td>
                                <td class="text-end fw-bold">{{ number_format($oa['jumlah'], 2) }}</td>
                                <td class="text-center">
                                    @php
                                        $oaColor = ($oaKey == 'OA10000') ? 'btn-primary' : 'btn-warning';
                                    @endphp
                                    <a href="{{ route('pentadbiran.dbus.edit', ['tahun'=>$tahun, 'kategori'=>$oaKey]) }}" class="btn btn-sm {{ $oaColor }} py-0 px-2" title="Kemaskini">
                                        <i class="fas fa-edit small"></i>
                                    </a>
                                </td>
                            </tr>

                            @foreach($oa['items'] as $osKey => $os)
                                {{-- BARIS OS (SUB-INDUK) --}}
                                <tr class="fw-bold text-primary bg-light">
                                    <td class="ps-4">{{ $osKey }} {{ $os['perkara'] }}</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($os['jumlah'], 2) }}</td>
                                    <td></td>
                                    <td class="text-center">
                                        
                                        {{-- 1. PECAHAN PEGAWAI (OS11, OS12, OS13) --}}
                                        @if(in_array($osKey, ['OS11000', 'OS12000', 'OS13000']))
                                            <a href="{{ route('pentadbiran.dbus.pecahan', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini Pecahan">
                                                <i class="fas fa-user small"></i>
                                            </a>
                                        
                                        {{-- 2. BAYARAN LEBIH MASA (OS14000) --}}
                                        @elseif($osKey == 'OS14000')
                                            <a href="{{ route('pentadbiran.dbus.edit_ol14101', ['kod'=>'OL14101', 'tahun'=>$tahun]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini OT">
                                                <i class="fas fa-edit small"></i>
                                            </a>

                                        {{-- 3. FAEDAH KEWANGAN LAIN (OS15000) --}}
                                        @elseif($osKey == 'OS15000')
                                            <a href="{{ route('pentadbiran.dbus.edit_os15000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini Faedah">
                                                <i class="fas fa-edit small"></i>
                                            </a>
                                            
                                        {{-- 4. PERJALANAN & SARA HIDUP (OS21000) --}}
                                        @elseif($osKey == 'OS21000')
                                            <a href="{{ route('pentadbiran.dbus.edit_os21000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-info py-0 px-2" title="Kemaskini Pecahan Perjalanan">
                                                <i class="fas fa-edit small"></i>
                                            </a>
                                            
                                        {{-- 5. PENGANGKUTAN BARANG (OS22000) --}}
                                        @elseif($osKey == 'OS22000')
                                            <a href="{{ route('pentadbiran.dbus.edit_os22000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-info py-0 px-2" title="Kemaskini Pengangkutan">
                                                <i class="fas fa-truck small"></i>
                                            </a>
                                        
                                        {{-- 6. PERHUBUNGAN DAN UTILITI (OS23000) - BARU! --}}
                                        @elseif($osKey == 'OS23000')
                                            <a href="{{ route('pentadbiran.dbus.edit_os23000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-info py-0 px-2" title="Kemaskini Utiliti">
                                                <i class="fas fa-lightbulb small"></i>
                                            </a>
                                            
                                        {{-- 7. SEMUA OS LAIN --}}
                                        @else
                                            <a href="{{ route('pentadbiran.dbus.edit', ['tahun'=>$tahun, 'kategori'=>$osKey]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini">
                                                <i class="fas fa-edit small"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>

                                @foreach($os['items'] as $olKey => $ol)
                                    {{-- BARIS OL (DETAIL) - KOSONGKAN BUTANG --}}
                                    <tr>
                                        <td class="ps-5 text-muted">{{ $olKey }} {{ $ol['perkara'] }}</td>
                                        <td class="text-end">{{ number_format($ol['jumlah'], 2) }}</td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">
                                            {{-- Tiada butang di sini --}}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <tr><td colspan="5" class="bg-white border-0 py-2"></td></tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr class="border-top border-dark">
                            <td class="text-end text-uppercase">Jumlah Anggaran Belanja Mengurus</td>
                            <td></td>
                            <td></td>
                            <td class="text-end text-success fs-5">{{ number_format($grandTotal, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection