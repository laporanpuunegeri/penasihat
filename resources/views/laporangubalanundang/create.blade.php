@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Daftar Laporan Penggubalan</h3>
    <form method="POST" action="{{ route('laporangubalanundang.store') }}">
        @csrf

        <div class="mb-3">
            <label for="tajuk" class="form-label">Tajuk Rang Undang-Undang / Perundangan Subsidiari Substantif</label>
            <input type="text" class="form-control" id="tajuk" name="tajuk" required>
        </div>

        <div class="mb-3">
            <label for="tindakan" class="form-label">Tindakan</label>
            <select class="form-select" id="tindakan" name="tindakan" required>
                <option value="">-- Sila Pilih Tindakan --</option>
                <option value="Menggubal dan menyemak perundangan utama">1. Menggubal dan menyemak perundangan utama</option>
                <option value="Menggubal dan menyemak perundangan subsidiari">2. Menggubal dan menyemak perundangan subsidiari</option>
                <option value="Semakan draf warta">3. Semakan draf warta</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status_preset" class="form-select mb-2">
                <option value="">-- Sila Pilih Status --</option>
                <option value="1. Draf Rang Undang-Undang telah dikemukakan kepada …………………. (Agensi) pada ………………..">
                    1. Draf Rang Undang-Undang telah dikemukakan kepada …………………. (Agensi) pada ………………..
                </option>
                <option value="2. Draf Undang-Undang Kecil/Perintah/Peraturan (sila pilih) telah dikemukakan kepada ……………………. (Agensi) pada ………………">
                    2. Draf Undang-Undang Kecil/Perintah/Peraturan (sila pilih) telah dikemukakan kepada ……………………. (Agensi) pada ………………
                </option>
                <option value="3. Dalam tindakan semakan selepas perbincangan/bengkel (sila pilih) dengan …………………. (Penasihat Undang-Undang/ wakil agensi) pada ……………………………..">
                    3. Dalam tindakan semakan selepas perbincangan/bengkel (sila pilih) dengan …………………. (Penasihat Undang-Undang/ wakil agensi) pada ……………………………..
                </option>
                <option value="4. Draf Rang Undang-Undang/ Undang-Undang Kecil/Perintah/Peraturan/warta (sila pilih) telah dikemukakan kepada Penasihat Undang-Undang pada ……………………..">
                    4. Draf Rang Undang-Undang/ Undang-Undang Kecil/Perintah/Peraturan/warta telah dikemukakan kepada Penasihat Undang-Undang pada ……………………..
                </option>
                <option value="5. Dalam tindakan semakan">
                    5. Dalam tindakan semakan
                </option>
            </select>

            <textarea name="status" id="status" class="form-control" rows="4" required>{{ old('status') }}</textarea>
        </div>

            @php $authUser = auth()->user(); @endphp

            @if ($authUser->role === 'user')
                <div class="form-check mb-3">
                    <input type="checkbox" name="hantar_kepada_boss" value="1" id="hantar_kepada_boss" class="form-check-input">
                    <label for="hantar_kepada_boss" class="form-check-label">Saya hadir bersama YB Penasihat</label>
                </div>
            @endif

            @if ($authUser->role === 'pa')
                <input type="hidden" name="hantar_kepada_boss" value="1">
            @endif

            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-send-check"></i> Hantar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const presetSelect = document.getElementById('status_preset');
        const textarea = document.getElementById('status');

        presetSelect.addEventListener('change', function () {
            if (this.value) {
                textarea.value = this.value;
                textarea.focus();
            }
        });
    });
</script>
@endsection
