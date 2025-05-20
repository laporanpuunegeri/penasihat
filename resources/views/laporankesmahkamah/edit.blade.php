@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">Kemaskini Laporan Kes Mahkamah</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm p-4 border-0">
        <form method="POST" action="{{ url('/laporankesmahkamah/' . $laporan->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="jenis_kes" class="form-label">*Jenis Kes / Pihak-Pihak</label>
                <select class="form-select" id="jenis_kes" name="jenis_kes" required>
                    <option value="">-- Sila Pilih --</option>
                    <option value="Jenayah" {{ old('jenis_kes', $laporan->jenis_kes) == 'Jenayah' ? 'selected' : '' }}>Jenayah</option>
                    <option value="Sivil" {{ old('jenis_kes', $laporan->jenis_kes) == 'Sivil' ? 'selected' : '' }}>Sivil</option>
                    <option value="Syariah" {{ old('jenis_kes', $laporan->jenis_kes) == 'Syariah' ? 'selected' : '' }}>Syariah</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="tarikh_sebutan" class="form-label">Tarikh Sebutan / Bicara</label>
                <input type="date" class="form-control" id="tarikh_sebutan" name="tarikh_sebutan"
                       value="{{ old('tarikh_sebutan', $laporan->tarikh_sebutan) }}" required>
            </div>

            <div class="mb-3">
                <label for="fakta_ringkas" class="form-label">Fakta Ringkas</label>
                <textarea class="form-control" id="fakta_ringkas" name="fakta_ringkas" rows="3" required>{{ old('fakta_ringkas', $laporan->fakta_ringkas) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="isu" class="form-label">Isu</label>
                <input type="text" class="form-control" id="isu" name="isu"
                       value="{{ old('isu', $laporan->isu) }}" required>
            </div>

            <div class="mb-3">
                <label for="skop_tugas" class="form-label">**Skop Tugas</label>
                <select class="form-select" id="skop_tugas" name="skop_tugas" required>
                    <option value="">-- Sila Pilih --</option>
                    @foreach([
                        'Meneliti dokumen perundangan',
                        'Menyediakan dokumen perundangan',
                        'Menyemak dokumen perundangan',
                        'Meluluskan dokumen perundangan',
                        'Mengendalikan kes mahkamah'
                    ] as $skop)
                        <option value="{{ $skop }}" {{ old('skop_tugas', $laporan->skop_tugas) == $skop ? 'selected' : '' }}>
                            {{ $skop }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="ringkasan_hujahan" class="form-label">Ringkasan Hujahan</label>
                <textarea class="form-control" id="ringkasan_hujahan" name="ringkasan_hujahan" rows="3" required>{{ old('ringkasan_hujahan', $laporan->ringkasan_hujahan) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" class="form-control" id="status" name="status"
                       value="{{ old('status', $laporan->status) }}" required>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-arrow-repeat"></i> Kemaskini
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
