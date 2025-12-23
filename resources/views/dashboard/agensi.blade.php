@extends('layouts.agensi')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Agensi</h1>
        {{-- Butang Report Optional --}}
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" onclick="window.print()">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak Laporan
        </button>
    </div>

    <div class="row">

        @foreach($stats as $item)
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                
                {{-- HEADER KAD --}}
                <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $item->tajuk }}</h6>
                    
                    <div class="dropdown no-arrow">
                        {{-- Cek dulu route wujud tak, kalau tak wujud disable button --}}
                        @if(Route::has($item->route))
                            <a href="{{ route($item->route) }}" class="btn btn-primary btn-sm shadow-sm">
                                <i class="fas fa-folder-open text-white-50"></i> Lihat Senarai
                            </a>
                        @else
                            <button class="btn btn-secondary btn-sm shadow-sm" disabled>
                                <i class="fas fa-lock text-white-50"></i> Belum Sedia
                            </button>
                        @endif
                    </div>
                </div>
                
                {{-- BODY KAD (STATISTIK) --}}
                <div class="card-body">
                    <div class="row no-gutters align-items-center text-center">
                        
                        {{-- 1. STATUS BARU / SEMAKAN --}}
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Dalam Semakan (Baru)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $item->baru }}</div>
                            <div class="mt-2 text-gray-300">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>

                        {{-- GARIS PEMISAH --}}
                        <div class="col-auto">
                            <div class="vr h-100 border-left" style="height: 50px; border-color: #e3e6f0;"></div>
                        </div>

                        {{-- 2. STATUS SELESAI / LULUS --}}
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Selesai / Lulus
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $item->selesai }}</div>
                            <div class="mt-2 text-gray-300">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>

                        {{-- GARIS PEMISAH --}}
                        <div class="col-auto">
                            <div class="vr h-100 border-left" style="height: 50px; border-color: #e3e6f0;"></div>
                        </div>

                        {{-- 3. JUMLAH KESELURUHAN --}}
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jumlah Permohonan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $item->total }}</div>
                            <div class="mt-2 text-gray-300">
                                <i class="fas fa-folder fa-2x text-info"></i>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection