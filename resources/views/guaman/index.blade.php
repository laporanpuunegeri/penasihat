{{-- resources/views/guaman/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Senarai Kes Guaman</h3>
        <a href="{{ route('guaman.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i> Daftar Kes Baharu
        </a>
    </div>
    
    {{-- BUTTON CETAK PDF --}}
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('guaman.cetak_laporan_pdf') }}" class="btn btn-info shadow-sm" target="_blank">
            <i class="fas fa-file-pdf me-2"></i> Cetak Laporan PDF
        </a>
    </div>

    {{-- FILTER UTAMA --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form action="{{ route('guaman.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="form-label fw-bold mb-0">Tapis Mengikut Kod Perkara:</label>
                </div>
                <div class="col-md-4">
                    <select name="kod" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Lihat Semua Kategori --</option>
                        @foreach ($categories as $kod => $cat)
                            <option value="{{ $kod }}" {{ $kodFilter == $kod ? 'selected' : '' }}>
                                KOD {{ $kod }} - {{ Str::limit($cat['title'], 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Tambah butang reset jika perlu --}}
            </form>
        </div>
    </div>


    {{-- SENARAI KES --}}
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
                                {{-- Link ke Edit / View Detail, kini menghala ke guaman.edit --}}
                                <a href="{{ route('guaman.edit', $case) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                {{-- Anda boleh tambah butang delete di sini jika perlu --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Tiada rekod kes ditemui.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection