@extends('layouts.agensi') 

@section('content')
<div class="container-fluid p-0">
    
    <div class="p-5 mb-4 rounded-3 text-white shadow" 
         style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%); position: relative; overflow: hidden;">
        
        <div style="position: relative; z-index: 2;">
            <h1 class="display-5 fw-bold">Selamat Datang!</h1>
            <p class="col-md-8 fs-4">Papan Pemuka Agensi: <span class="text-info fw-bold">{{ auth()->guard('agensi')->user()->nama_agensi }}</span></p>
        </div>
        
        <i class="fas fa-building fa-10x" style="position: absolute; right: 20px; bottom: -20px; opacity: 0.1; color: white;"></i>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="fas fa-file-invoice fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted text-uppercase fw-bold mb-1">Permohonan Aktif</h6>
                        <h2 class="mb-0 fw-bold">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted text-uppercase fw-bold mb-1">Menunggu Tindakan</h6>
                        <h2 class="mb-0 fw-bold">-</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection