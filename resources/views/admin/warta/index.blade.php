@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pengurusan Semakan Warta</h6>
            <p class="mb-0 small text-muted">Modul pengesahan dan semakan dokumen warta jabatan.</p>
        </div>

        <div class="card-body">
            
            {{-- PILIHAN SEKSYEN --}}
            <div class="row mb-4">
                <div class="col-md-4 offset-md-8">
                    <form action="{{ route('admin.warta.index') }}" method="GET">
                        <label class="small font-weight-bold text-dark">Pilih Jenis Seksyen:</label>
                        <select name="type" class="form-control border-dark" onchange="this.form.submit()">
                            <option value="12" {{ $seksyen == '12' ? 'selected' : '' }}>Seksyen 12</option>
                            <option value="62" {{ $seksyen == '62' ? 'selected' : '' }}>Seksyen 62</option>
                            <option value="64" {{ $seksyen == '64' ? 'selected' : '' }}>Seksyen 64</option>
                            <option value="97" {{ $seksyen == '97' ? 'selected' : '' }}>Seksyen 97 & 98</option>
                            <option value="130" {{ $seksyen == '130' ? 'selected' : '' }}>Seksyen 130</option>
                            <option value="168" {{ $seksyen == '168' ? 'selected' : '' }}>Seksyen 168</option>
                            <option value="175a" {{ $seksyen == '175a' ? 'selected' : '' }}>Seksyen 175A</option>
                            <option value="175d" {{ $seksyen == '175d' ? 'selected' : '' }}>Seksyen 175D</option>
                            <option value="261" {{ $seksyen == '261' ? 'selected' : '' }}>Seksyen 261</option>
                            <option value="263" {{ $seksyen == '263' ? 'selected' : '' }}>Seksyen 263</option>
                            <option value="326" {{ $seksyen == '326' ? 'selected' : '' }}>Seksyen 326</option>
                        </select>
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                    </form>
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <form action="{{ route('admin.warta.index') }}" method="GET" class="mb-4">
                <input type="hidden" name="type" value="{{ $seksyen }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control border-primary" placeholder="Cari No. Fail atau Nama Agensi..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </div>
            </form>

            {{-- JADUAL SENARAI --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-dark" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-dark font-weight-bold">
                            <th style="width: 5%" class="text-center">Bil</th>
                            <th style="width: 20%">Agensi</th>
                            <th style="width: 25%">No. Fail</th>
                            <th style="width: 15%" class="text-center">Tarikh Hantar</th>
                            <th style="width: 15%" class="text-center">Status Semakan</th>
                            <th style="width: 20%" class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($senarai as $item)
                        
                        @php
                            // Check status, kalau kosong kita set 'Baru'
                            $statusRaw = $item->status;
                            if (empty($statusRaw)) {
                                $statusRaw = 'Baru';
                            }
                            $noFail = $item->no_fail ?? '-';
                        @endphp

                        <tr>
                            <td class="text-center text-dark align-middle">{{ $loop->iteration }}</td>
                            <td class="align-middle">
                                <strong class="text-dark">{{ $item->agensi->nama_agensi ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $item->agensi->nama_pegawai ?? '-' }}</small>
                            </td>
                            
                            {{-- NO FAIL --}}
                            <td class="align-middle">
                                <span class="text-primary font-weight-bold" style="font-size: 1.1em;">
                                    {{ $noFail }}
                                </span>
                            </td>

                            <td class="text-center text-dark align-middle">{{ $item->created_at->format('d/m/Y') }}</td>
                            
                            {{-- STATUS (SAYA PAKSA WARNA GUNA STYLE) --}}
                            <td class="text-center align-middle">
                                @if($statusRaw == 'Baru')
                                    {{-- BIRU GELAP + TULISAN PUTIH --}}
                                    <span class="badge px-3 py-2 shadow-sm" style="background-color: #0056b3; color: #ffffff; font-size: 0.9em; border: 1px solid #004494;">
                                        BARU
                                    </span>
                                @elseif($statusRaw == 'Telah Disemak')
                                    {{-- HIJAU + TULISAN PUTIH --}}
                                    <span class="badge px-3 py-2 shadow-sm" style="background-color: #1cc88a; color: #ffffff; font-size: 0.9em; border: 1px solid #169b6b;">
                                        SELESAI
                                    </span>
                                @else
                                    {{-- KELABU + TULISAN HITAM (DEFAULT) --}}
                                    <span class="badge px-3 py-2 shadow-sm" style="background-color: #eaecf4; color: #3a3b45; font-size: 0.9em; border: 1px solid #d1d3e2;">
                                        {{ strtoupper($statusRaw) }}
                                    </span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center">
                                    {{-- BUTANG LIHAT --}}
                                    <a href="{{ route('admin.warta.show', ['id' => $item->id, 'type' => $seksyen]) }}" 
                                       class="btn btn-sm shadow-sm mr-2 font-weight-bold text-white" 
                                       style="background-color: #36b9cc; border-color: #36b9cc;"
                                       target="_blank">
                                        <i class="fas fa-eye"></i> Lihat / Cetak
                                    </a>

                                    {{-- BUTANG SAHKAN --}}
                                    @if($statusRaw != 'Telah Disemak')
                                    <form action="{{ route('admin.warta.sahkan', ['id' => $item->id, 'type' => $seksyen]) }}" method="POST" onsubmit="return confirm('Sahkan rekod ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm shadow-sm font-weight-bold text-white"
                                                style="background-color: #1cc88a; border-color: #1cc88a;">
                                            <i class="fas fa-check"></i> Sahkan
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-dark">
                                <i class="fas fa-folder-open fa-2x mb-3 text-gray-400"></i><br>
                                Tiada rekod ditemui bagi <strong>Seksyen {{ strtoupper($seksyen) }}</strong>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

</div>
@endsection