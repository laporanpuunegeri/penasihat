@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Lampiran Kes Mahkamah (Lampiran II)</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Pilihan bulan & tahun --}}
    <form method="GET" action="{{ route('lampiran.index') }}" class="row mb-3" id="filterForm">
        <div class="col-md-2">
            <select name="bulan" class="form-control" onchange="document.getElementById('filterForm').submit();">
                @foreach (range(1, 12) as $b)
                    <option value="{{ $b }}" {{ $b == $bulan ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="tahun" class="form-control" onchange="document.getElementById('filterForm').submit();">
                @foreach (range(now()->year, now()->year - 5) as $t)
                    <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Form Simpan --}}
    <form method="POST" action="{{ route('lampiran.store') }}" id="lampiranForm">
        @csrf
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <table class="table table-bordered">
            <thead class="table-secondary text-center">
                <tr>
                    <th>Kategori</th>
                    <th>Bil. Aktif</th>
                    <th>Majistret</th>
                    <th>Sesi</th>
                    <th>Tinggi</th>
                    <th>Rayuan</th>
                    <th>Persk.</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kategori_list as $kategori)
                    @php $item = $lampiran[$kategori] ?? null; @endphp
                    <tr>
                        <td>{{ $kategori }}</td>
                        <td><input type="number" name="data[{{ $kategori }}][bil_aktif]" value="{{ $item->bil_aktif ?? 0 }}" class="form-control text-center"></td>
                        <td><input type="number" name="data[{{ $kategori }}][majistret]" value="{{ $item->majistret ?? 0 }}" class="form-control text-center"></td>
                        <td><input type="number" name="data[{{ $kategori }}][sesi]" value="{{ $item->sesi ?? 0 }}" class="form-control text-center"></td>
                        <td><input type="number" name="data[{{ $kategori }}][tinggi]" value="{{ $item->tinggi ?? 0 }}" class="form-control text-center"></td>
                        <td><input type="number" name="data[{{ $kategori }}][rayuan]" value="{{ $item->rayuan ?? 0 }}" class="form-control text-center"></td>
                        <td><input type="number" name="data[{{ $kategori }}][persk]" value="{{ $item->persk ?? 0 }}" class="form-control text-center"></td>
                        <td><input type="text" name="data[{{ $kategori }}][status]" value="{{ $item->status ?? '-' }}" class="form-control"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" id="simpanBtn" class="btn btn-success">💾 Simpan Lampiran II</button>
        </div>
    </form>
</div>
@endsection
