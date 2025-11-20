@extends('layouts.app') 

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Kewangan</h1>
        
        <div class="d-flex align-items-center">
            
            <form action="{{ route('kewangan.index') }}" method="GET" class="d-flex align-items-center mr-2">
                <label for="tahunSelect" class="mb-0 mr-2 font-weight-bold text-gray-700">Tahun:</label>
                <select name="tahun" id="tahunSelect" 
                        class="form-control form-control-sm border-primary font-weight-bold text-primary shadow-sm" 
                        onchange="this.form.submit()" 
                        style="cursor: pointer; width: auto;">
                    @php
                        $startYear = 2026; 
                        $endYear = 2020;   
                    @endphp
                    @for($y = $startYear; $y >= $endYear; $y--)
                        <option value="{{ $y }}" {{ $tahun_dipilih == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </form>

            <a href="{{ route('kewangan.create') }}" class="btn btn-sm btn-primary shadow-sm text-nowrap">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Rekod Baru
            </a>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-12 text-left">
            <h4 class="m-0 font-weight-bold text-gray-900 text-uppercase">
                ANGGARAN PERBELANJAAN MENGURUS TAHUN {{ $tahun_dipilih }} MENGIKUT OBJEK AM DAN SEBAGAI
            </h4>
            <h4 class="m-0 font-weight-bold text-gray-900 text-uppercase">
                MAKSUD BEKALAN: B.08 - JABATAN PEGUAM NEGARA, MALAYSIA
            </h4>
            <h4 class="m-0 font-weight-bold text-gray-900 text-uppercase">
                PROGRAM: 010000 - PENGURUSAN
            </h4>
            <h4 class="m-0 font-weight-bold text-gray-900 text-uppercase">
                AKTIVITI: 010300 - PEJABAT PENASIHAT UNDANG-UNDANG NEGERI {{ Auth::user()->negeri ?? '' }}
            </h4>
            <hr class="mt-3 mb-0 border-dark">
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Jumlah Peruntukan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                RM {{ number_format($grand_total_peruntukan, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Jumlah Belanja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                RM {{ number_format($grand_total_belanja, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Baki Semasa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                RM {{ number_format($grand_total_baki, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            @php
                $statusColor = $grand_peratus > 100 ? 'danger' : 'info';
            @endphp

            <div class="card border-left-{{ $statusColor }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $statusColor }} text-uppercase mb-1">Prestasi Belanja
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($grand_peratus, 1) }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-{{ $statusColor }}" role="progressbar"
                                            style="width: {{ $grand_peratus }}%" aria-valuenow="{{ $grand_peratus }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion" id="accordionKewangan">

        @foreach($laporan_kewangan as $kod_utama => $data)
            @php
                $baki_utama = $data['total_peruntukan'] - $data['total_belanja'];
                $peratus_utama = $data['total_peruntukan'] > 0 ? ($data['total_belanja'] / $data['total_peruntukan']) * 100 : 0;
                $collapseId = "collapse" . $kod_utama;
            @endphp

            <div class="card shadow mb-3">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" 
                     id="heading{{ $kod_utama }}" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#{{ $collapseId }}" 
                     aria-expanded="false" 
                     aria-controls="{{ $collapseId }}"
                     style="cursor: pointer; background-color: #f8f9fc;">
                    
                    <div>
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-folder mr-2"></i> {{ $kod_utama }} - {{ $data['tajuk'] }}
                        </h6>
                    </div>

                    <div class="d-none d-md-block">
                        <span class="badge bg-secondary mr-1">Siling: RM {{ number_format($data['total_peruntukan'], 0) }}</span>
                        <span class="badge bg-danger mr-1">Belanja: RM {{ number_format($data['total_belanja'], 0) }}</span>
                        <span class="badge bg-success">Baki: RM {{ number_format($baki_utama, 0) }}</span>
                    </div>
                </div>

                <div id="{{ $collapseId }}" class="collapse" aria-labelledby="heading{{ $kod_utama }}" data-parent="#accordionKewangan">
                    <div class="card-body">
                        
                        @if(count($data['items']) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0">
                                    <thead class="table-secondary text-center">
                                        <tr>
                                            <th style="width: 10%">KOD OBJEK</th>
                                            <th style="width: 35%">BUTIRAN / JENIS PERBELANJAAN</th>
                                            <th style="width: 12%">PERUNTUKAN (RM)</th>
                                            <th style="width: 12%">BELANJA (RM)</th>
                                            <th style="width: 12%">BAKI (RM)</th>
                                            <th style="width: 5%">%</th>
                                            <th style="width: 14%">TINDAKAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['items'] as $item)
                                            @php
                                                $baki_item = $item['siling'] - $item['belanja'];
                                                $peratus_item = $item['siling'] > 0 ? ($item['belanja'] / $item['siling']) * 100 : 0;
                                                
                                                $progress_color = 'bg-success';
                                                if($peratus_item > 70) $progress_color = 'bg-warning';
                                                if($peratus_item > 90) $progress_color = 'bg-danger';
                                            @endphp
                                            <tr>
                                                <td class="text-center font-weight-bold">{{ $item['kod'] }}</td>
                                                <td>{{ $item['butiran'] }}</td>
                                                <td class="text-right">{{ number_format($item['siling'], 2) }}</td>
                                                <td class="text-right text-danger">{{ number_format($item['belanja'], 2) }}</td>
                                                <td class="text-right text-success font-weight-bold">{{ number_format($baki_item, 2) }}</td>
                                                <td class="text-center">
                                                    <div class="progress progress-sm" title="{{ number_format($peratus_item, 1) }}%">
                                                        <div class="progress-bar {{ $progress_color }}" role="progressbar" style="width: {{ $peratus_item }}%"></div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('kewangan.edit', $item['id']) }}" class="btn btn-warning btn-sm mr-1">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    
                                                    <form action="{{ route('kewangan.destroy', $item['id']) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                        <tr class="table-light font-weight-bold text-gray-800">
                                            <td colspan="2" class="text-right">JUMLAH BESAR {{ $kod_utama }}:</td>
                                            <td class="text-right">{{ number_format($data['total_peruntukan'], 2) }}</td>
                                            <td class="text-right">{{ number_format($data['total_belanja'], 2) }}</td>
                                            <td class="text-right">{{ number_format($baki_utama, 2) }}</td>
                                            <td class="text-center">{{ number_format($peratus_utama, 0) }}%</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Tiada rekod dijumpai untuk kategori {{ $kod_utama }} pada tahun {{ $tahun_dipilih }}.</p>
                                <a href="{{ route('kewangan.create') }}" class="btn btn-sm btn-outline-primary">Tambah Rekod Sekarang</a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach

    </div>

</div>

@push('scripts')
<script>
    $(document).ready(function(){
        $('#collapse10000').addClass('show');
    });
</script>
@endpush

@endsection