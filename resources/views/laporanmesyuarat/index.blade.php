@extends('layouts.app')

@section('content')
<div class="container">
    <h5 class="fw-bold text-center text-uppercase mb-4">Laporan Mesyuarat Yang Dihadiri</h5>

    <p class="fst-italic text-center mb-2">
        (*Sila nyatakan dan rujukan butiran berkenaan sekiranya pandangan undang-undang telah diberikan dalam <strong>LAMPIRAN I</strong>)
    </p>
    <p class="fst-italic text-center mb-4">
        (**Bagi Kes Tatatertib, sila nyatakan bilangan kes yang telah didengar)
    </p>

    {{-- Tapisan --}}
    <form method="GET" action="{{ url('/laporanmesyuarat') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-auto">
            <label for="bulan" class="form-label">Tapis Ikut Bulan:</label>
        </div>
        <div class="col-auto">
            <select name="bulan" id="bulan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Semua Bulan --</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-auto">
            <a href="{{ url('/laporanmesyuarat') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
        <div class="col text-end">
            <a href="{{ url('/laporanmesyuarat/create') }}" class="btn btn-success">+ Daftar Baharu</a>
        </div>
    </form>

    {{-- Jadual --}}
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th rowspan="2">BIL</th>
                    <th rowspan="2">TARIKH DAFTAR</th>
                    <th rowspan="2">MESYUARAT</th>
                    <th rowspan="2">ISU</th>
                    <th rowspan="2">TARIKH MESYUARAT</th>
                    <th rowspan="2">STATUS</th>
                    <th colspan="2">PANDANGAN</th>
                    <th rowspan="2">TINDAKAN</th>
                </tr>
                <tr>
                    <th>LISAN</th>
                    <th>BERTULIS</th>
                </tr>
            </thead>
            <tbody>
                @php $user = auth()->user(); @endphp
                @forelse ($data as $index => $laporan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ optional($laporan->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-start">{{ $laporan->mesyuarat }}</td>
                        <td class="text-start">{{ $laporan->isu }}</td>
                        <td>{{ optional($laporan->tarikh_mesyuarat)->format('d/m/Y') }}</td>
                        <td>{{ $laporan->status }}</td>
                        <td>{{ $laporan->pandangan === 'Lisan' ? '✔' : '' }}</td>
                        <td>{{ $laporan->pandangan === 'Bertulis' ? '✔' : '' }}</td>
                        <td>
                            @if ($user->role === 'pa' || $laporan->user_id === $user->id)
                                <a href="{{ url('/laporanmesyuarat/' . $laporan->id . '/edit') }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                                <form action="{{ url('/laporanmesyuarat/' . $laporan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda pasti untuk padam?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Padam</button>
                                </form>
                            @else
                                <span class="text-muted fst-italic">Semakan Sahaja</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-muted">Tiada data direkodkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
