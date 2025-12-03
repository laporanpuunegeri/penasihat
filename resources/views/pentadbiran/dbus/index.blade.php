@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">D'BUS (OBB) - Anggaran Belanja</h3>
            <p class="text-muted small">BAHAGIAN/PUUN/JPN : PENASIHAT UNDANG-UNDANG NEGERI MELAKA</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-secondary shadow-sm"><i class="fas fa-print me-2"></i> Cetak</button>
            <a href="{{ route('pentadbiran.dbus.create') }}" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i> Daftar Baru</a>
        </div>
    </div>

    {{-- FILTER TAHUN --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <label class="fw-bold text-secondary me-3 mb-0">TAHUN ANGGARAN:</label>
                <form action="{{ route('pentadbiran.dbus.index') }}" method="GET">
                    <select name="tahun" class="form-select form-select-sm fw-bold border-primary text-primary" style="width: 150px;" onchange="this.form.submit()">
                        {{-- Mula dari 2026 hingga 2030 --}}
                        @for ($y = 2026; $y <= 2030; $y++)
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
                            <tr class="table-secondary border-bottom-2 border-dark">
                                <td class="fw-bold text-dark">{{ $oaKey }} {{ $oa['perkara'] }}</td>
                                <td></td>
                                <td></td>
                                <td class="text-end fw-bold">{{ number_format($oa['jumlah'], 2) }}</td>
                                <td class="text-center">
                                    {{-- PERATURAN WARNA OA --}}
                                    @php
                                        $oaColor = ($oaKey == 'OA10000') ? 'btn-primary' : 'btn-warning';
                                    @endphp
                                    <a href="{{ route('pentadbiran.dbus.edit', ['tahun'=>$tahun, 'kategori'=>$oaKey]) }}" class="btn btn-sm {{ $oaColor }} py-0 px-2" title="Kemaskini">
                                        <i class="fas fa-edit small"></i>
                                    </a>
                                </td>
                            </tr>

                            @foreach($oa['items'] as $osKey => $os)
                                <tr class="fw-bold text-primary bg-light">
                                    <td class="ps-4">{{ $osKey }} {{ $os['perkara'] }}</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($os['jumlah'], 2) }}</td>
                                    <td></td>
                                    <td class="text-center">
                                        
                                        {{-- LOGIK PECAHAN OS (Gaji, Elaun, KWSP) - KEKAL BIRU MUDA (INFO) --}}
                                        @if($osKey == 'OS11000' || $osKey == 'OS12000' || $osKey == 'OS13000')
                                            <a href="{{ route('pentadbiran.dbus.pecahan', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-info py-0 px-2 text-white" title="Pecahan Pegawai/Gaji">
                                                <i class="fas fa-users small"></i>
                                            </a>
                                        
                                        {{-- LOGIK PECAHAN KHAS OS14000 (Bayaran Lebih Masa) - KUNING --}}
                                        @elseif($osKey == 'OS14000')
                                            <a href="{{ route('pentadbiran.dbus.edit_ol14101', ['kod'=>'OL14101', 'tahun'=>$tahun]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini Bayaran Lebih Masa">
                                                <i class="fas fa-edit small"></i>
                                            </a>

                                        {{-- LOGIK PECAHAN KHAS OS15000 (Faedah Kewangan Lain) - KUNING --}}
                                        @elseif($osKey == 'OS15000')
                                            <a href="{{ route('pentadbiran.dbus.edit_os15000', ['kod'=>$osKey, 'tahun'=>$tahun]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini Faedah Kewangan">
                                                <i class="fas fa-edit small"></i>
                                            </a>
                                            
                                        {{-- BUTANG EDIT OS LAIN (OS21000 ke atas, yang tiada pecahan khas) - KUNING --}}
                                        @else
                                            <a href="{{ route('pentadbiran.dbus.edit', ['tahun'=>$tahun, 'kategori'=>$osKey]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini">
                                                <i class="fas fa-edit small"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>

                                @foreach($os['items'] as $olKey => $ol)
                                    <tr>
                                        <td class="ps-5 text-muted">{{ $olKey }} {{ $ol['perkara'] }}</td>
                                        <td class="text-end">{{ number_format($ol['jumlah'], 2) }}</td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">
                                            {{-- BUTANG EDIT OL --}}
                                            {{-- KITA SEKAT EDIT PADA OL YANG DIURUS OLEH INDUK OS KHAS (OS14000, OS15000) --}}
                                            @if(!in_array($osKey, ['OS14000', 'OS15000']))
                                                <a href="{{ route('pentadbiran.dbus.edit', ['tahun'=>$tahun, 'kategori'=>$olKey]) }}" class="btn btn-sm btn-warning py-0 px-2" title="Kemaskini">
                                                    <i class="fas fa-edit small"></i>
                                                </a>
                                            @endif
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