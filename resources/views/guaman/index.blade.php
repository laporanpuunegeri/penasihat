@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Senarai Kes Guaman</h3>
        <a href="{{ route('guaman.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i> Daftar Kes Baharu
        </a>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('guaman.cetak_laporan_pdf', ['bulan' => $bulanDipilih, 'tahun' => $tahunDipilih]) }}" class="btn btn-info shadow-sm" target="_blank">
            <i class="fas fa-file-pdf me-2"></i> Cetak Laporan PDF ({{ $bulanDipilih }}/{{ $tahunDipilih }})
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light fw-bold text-uppercase small">
            <i class="fas fa-filter me-1"></i> Tapis Rekod
        </div>
        <div class="card-body py-3">
            <form action="{{ route('guaman.index') }}" method="GET" class="row g-3 align-items-end">
                
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Kod Perkara</label>
                    <select name="kod" class="form-select form-select-sm">
                        <option value="">-- Semua Kod --</option>
                        @foreach ($categories as $kod => $cat)
                            <option value="{{ $kod }}" {{ $kodFilter == $kod ? 'selected' : '' }}>
                                KOD {{ $kod }} - {{ Str::limit($cat['title'], 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        @php
                            $months = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Mac', '04' => 'April',
                                '05' => 'Mei', '06' => 'Jun', '07' => 'Julai', '08' => 'Ogos',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Disember'
                            ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $bulanDipilih == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm">
                        @foreach($senaraiTahun as $thn)
                            <option value="{{ $thn }}" {{ $tahunDipilih == $thn ? 'selected' : '' }}>
                                {{ $thn }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                        <i class="fas fa-search me-1"></i> Lihat Rekod
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info py-2 small border-0 shadow-sm mb-4">
        <i class="fas fa-info-circle me-2"></i> 
        Memaparkan rekod bagi bulan <strong>{{ \Carbon\Carbon::createFromDate(null, $bulanDipilih, null)->translatedFormat('F') }} {{ $tahunDipilih }}</strong>.
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="fw-bold text-primary mb-3">Kes Ditemui: {{ $cases->count() }}</h5>

            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0" style="font-size: 0.8rem;">
                    <thead class="table-dark text-uppercase text-center">
                        <tr>
                            <th style="width: 5%">Kod</th>
                            <th style="width: 15%">Rujukan Fail / Mahkamah</th>
                            <th style="width: 30%">Butiran Pihak Berlawanan (Plaintif V. Defendan)</th>
                            <th style="width: 10%">Kategori Kes</th>
                            <th style="width: 10%">Status Semasa</th>
                            <th style="width: 10%">Tarikh Buka</th>
                            <th style="width: 10%">Kendalian Oleh</th>
                            <th style="width: 10%">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cases as $case)
                            <tr>
                                <td class="fw-bold">{{ $case->kod_perkara }}</td>
                                <td class="text-start">
                                    Ruj Fail: {{ $case->rujukan_fail }}<br>
                                    Mahkamah: <span class="fw-bold">{{ $case->mahkamah }}</span>
                                </td>
                                <td class="text-start">{{ Str::limit($case->pihak_berlawanan, 150) }}</td>
                                <td>{{ $case->kategori_kes }}</td>
                                <td><span class="badge bg-info">{{ $case->status_kes }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($case->tarikh_buka)->format('d/m/Y') }}</td>
                                <td>{{ $case->kendalian_oleh }}</td>
                                <td>
                                    <a href="{{ route('guaman.edit', $case) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Tiada rekod kes ditemui pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $cases->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection