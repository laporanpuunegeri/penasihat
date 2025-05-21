@extends('layouts.app')

@section('content')
<div class="container-fluid px-5">
    <h3 class="mb-4">Laporan Pandangan Undang-Undang: Daftar Baharu</h3>

    <div class="card w-100 shadow-sm p-4">
        <form method="POST" action="{{ route('laporanpandanganundang.store') }}">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select name="kategori" id="kategori" class="form-select" required>
                        <option value="">-- Sila Pilih Kategori --</option>
                        @foreach(['Perlembagaan', 'Tanah / PBT', 'Undang-Undang Pentadbiran / Perkhidmatan', 'Perjanjian / MOU', 'Penswastaan', 'Lain-lain'] as $kategori)
                            <option value="{{ $kategori }}" {{ old('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="tarikh_terima" class="form-label">Tarikh Terima</label>
                    <input type="date" name="tarikh_terima" id="tarikh_terima" class="form-control" value="{{ old('tarikh_terima') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="isu" class="form-label">Tajuk Isu</label>
                <input type="text" name="isu" id="isu" class="form-control" value="{{ old('isu') }}" required>
            </div>

            <div class="mb-3">
                <label for="fakta_ringkasan" class="form-label">Fakta Ringkasan</label>
                <textarea name="fakta_ringkasan" id="fakta_ringkasan" class="form-control" rows="3" required>{{ old('fakta_ringkasan') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="isu_detail" class="form-label">Perincian Isu</label>
                <textarea name="isu_detail" id="isu_detail" class="form-control" rows="2" required>{{ old('isu_detail') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Pandangan</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_pandangan" value="Lisan" id="lisan" {{ old('jenis_pandangan') === 'Lisan' ? 'checked' : '' }}>
                    <label class="form-check-label" for="lisan">Lisan</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_pandangan" value="Bertulis" id="bertulis" {{ old('jenis_pandangan') === 'Bertulis' ? 'checked' : '' }}>
                    <label class="form-check-label" for="bertulis">Bertulis</label>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status_preset" class="form-label">Pilih Status</label>
                    <select id="status_preset" class="form-select mb-2">
                        <option value="">-- Sila Pilih Status --</option>
                        @php
                            $senarai_status = [
                                'Pandangan undang-undang/Maklum Balas telah dikemukakan /melalui e-mel kepada ………. pada …… Julai 20XX',
                                'Draf pandangan undang-undang telah dikemukakan kepada Penasihat Undang-Undang pada ….. Mei 20XX',
                                'Dalam tindakan untuk menyediakan pandangan undang-undang selepas perbincangan dengan YB PUU/Agensi pada … Mei 20XX',
                                'Dalam tindakan untuk mendapatkan dokumen atau maklum balas daripada Agensi ………. melalui surat/email/percakapan/Whatsapps/telefon bertarikh……. Mei 20XX',
                                'Dalam tindakan untuk menyediakan pandangan undang-undang atau maklum balas selepas menerima dokumen daripada Agensi ……… bertarikh ……. Mei 20XX',
                                'Cadangan untuk digugurkan daripada Laporan sehingga mendapat tarikh daripada agensi untuk perbincangan',
                                'Draf Perjanjian telah dikemukakan kepada …………………….. pada …………… September 20XX',
                                'Pandangan undang-undang telah dikemukakan dalam mesyuarat pada …………………………',
                            ];
                        @endphp
                        @foreach ($senarai_status as $status_item)
                            <option value="{{ $status_item }}">{{ $loop->iteration }}. {{ Str::limit($status_item, 60) }}</option>
                        @endforeach
                    </select>
                    <textarea name="status" id="status" class="form-control" rows="4" required>{{ old('status') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label for="tarikh_selesai" class="form-label">Tarikh Selesai</label>
                    <input type="date" name="tarikh_selesai" id="tarikh_selesai" class="form-control" value="{{ old('tarikh_selesai') }}">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="belum_selesai" value="1" id="belum_selesai" {{ old('belum_selesai') ? 'checked' : '' }}>
                        <label class="form-check-label" for="belum_selesai">Belum Selesai</label>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="dirujuk_jpn" value="1" id="dirujuk_jpn" {{ old('dirujuk_jpn') ? 'checked' : '' }}>
                        <label class="form-check-label" for="dirujuk_jpn">Dirujuk ke JPN (Ibu Pejabat)</label>
                    </div>
                </div>
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
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Ralat!</strong> Sila semak semula input anda.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
});
            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-send-check"></i> Hantar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('status_preset').addEventListener('change', function () {
            const statusTextarea = document.getElementById('status');
            if (this.value) {
                statusTextarea.value = this.value;
            }
        });

        const tarikhField = document.getElementById("tarikh_selesai");
        const checkbox = document.getElementById("belum_selesai");

        function toggleFields() {
            if (checkbox.checked) {
                tarikhField.value = "";
                tarikhField.disabled = true;
            } else {
                tarikhField.disabled = false;
            }

            if (tarikhField.value !== "") {
                checkbox.checked = false;
            }
        }

        tarikhField.addEventListener("change", toggleFields);
        checkbox.addEventListener("change", toggleFields);
        toggleFields();
    });
</script>
@endsection
