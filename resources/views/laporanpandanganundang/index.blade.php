@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h3 class="mb-4">Senarai Laporan Pandangan Undang-Undang</h3>

    {{-- Tapisan ikut bulan --}}
    <form method="GET" class="row g-3 mb-3 align-items-end">
        <div class="col-md-3">
            <label for="bulan" class="form-label">Tapis Ikut Bulan:</label>
            <select name="bulan" id="bulan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih Bulan --</option>
                @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April', 5 => 'Mei', 6 => 'Jun',
                           7 => 'Julai', 8 => 'Ogos', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember']
                           as $num => $nama)
                    <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <a href="{{ route('laporanpandanganundang.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('laporanpandanganundang.create') }}" class="btn btn-success">+ Daftar Baharu</a>
        </div>
    </form>

    @php
        $kategori_list = [
            'Perlembagaan',
            'Tanah / PBT',
            'Undang-Undang Pentadbiran / Perkhidmatan',
            'Perjanjian / MOU',
            'Penswastaan',
            'Lain-lain'
        ];
        $currentUser = auth()->user();
    @endphp

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Tarikh Daftar</th>
                    <th>Tarikh Terima</th>
                    <th>Fakta Ringkasan</th>
                    <th>Isu</th>
                    <th>Ringkasan Pandangan</th>
                    <th colspan="2">Jenis Pandangan</th>
                    <th>Status / Tarikh Selesai</th>
                    <th>Tindakan</th>
                </tr>
            </thead>

            @foreach ($kategori_list as $kategori)
                @php $filtered = $data->where('kategori', $kategori); @endphp

                <tbody>
                    <tr class="table-secondary">
                        <td colspan="9"><strong>({{ $loop->iteration }}) {{ strtoupper($kategori) }}</strong></td>
                    </tr>

                    @forelse ($filtered as $item)
                        <tr>
                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($item->tarikh_terima)->format('d/m/Y') }}</td>
                            <td class="text-start">{{ $item->fakta_ringkasan }}</td>
                            <td class="text-start">{{ $item->isu_detail }}</td>
                            <td class="text-start">{{ $item->ringkasan_pandangan }}</td>
                            <td>{{ $item->jenis_pandangan === 'Lisan' ? '✔' : '' }}</td>
                            <td>{{ $item->jenis_pandangan === 'Bertulis' ? '✔' : '' }}</td>
                            <td class="text-start">{{ $item->status }}</td>
                            <td>
                                @if ($currentUser->role === 'pa')
                                    <div class="text-muted small fst-italic mb-1">
                                        {{ optional($item->creator)->name ?? 'Pegawai tidak dikenalpasti' }}
                                    </div>
                                    <a href="{{ route('laporanpandanganundang.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('laporanpandanganundang.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Padam laporan ini?')">Padam</button>
                                    </form>
                                @elseif ($currentUser->role === 'yb')
                                    <span class="text-muted fst-italic">
                                        {{ optional($item->creator)->name ?? 'Pegawai tidak dikenalpasti' }}
                                    </span>
                                @else
                                    <a href="{{ route('laporanpandanganundang.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('laporanpandanganundang.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Padam laporan ini?')">Padam</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted">Tiada rekod untuk kategori ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            @endforeach
        </table>
    </div>
</div>
@endsection
