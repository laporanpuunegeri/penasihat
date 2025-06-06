@extends('layouts.app')

@section('content')
<div class="container-fluid px-5">
    <h3 class="mb-4">Kemaskini Laporan Pandangan Undang-Undang</h3>

    <div class="card w-100 shadow-sm p-4">
        <form method="POST" action="{{ route('laporanpandanganundang.update', $laporan->id) }}">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="isu" class="form-label">Isu</label>
                    <input type="text" name="isu" id="isu" class="form-control" value="{{ $laporan->isu }}" required>
                </div>
                <div class="col-md-6">
                    <label for="tarikh_terima" class="form-label">Tarikh Terima</label>
                    <input type="date" name="tarikh_terima" id="tarikh_terima" class="form-control" value="{{ $laporan->tarikh_terima->format('Y-m-d') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="fakta_ringkasan" class="form-label">Fakta Ringkasan</label>
                <textarea name="fakta_ringkasan" class="form-control" required>{{ $laporan->fakta_ringkasan }}</textarea>
            </div>

            <div class="mb-3">
                <label for="isu_detail" class="form-label">Isu Detail</label>
                <textarea name="isu_detail" class="form-control" required>{{ $laporan->isu_detail }}</textarea>
            </div>

            <div class="mb-3">
                <label for="ringkasan_pandangan" class="form-label">Ringkasan Pandangan</label>
                <textarea name="ringkasan_pandangan" class="form-control" required>{{ $laporan->ringkasan_pandangan }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Pandangan</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_pandangan" value="Lisan" id="lisan"
                        {{ (old('jenis_pandangan', $laporan->jenis_pandangan ?? '') === 'Lisan') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lisan">Lisan</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_pandangan" value="Bertulis" id="bertulis"
                        {{ (old('jenis_pandangan', $laporan->jenis_pandangan ?? '') === 'Bertulis') ? 'checked' : '' }}>
                    <label class="form-check-label" for="bertulis">Bertulis</label>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status_preset" class="form-label">Pilih Status</label>
                    <select id="status_preset" class="form-select mb-2">
                        <option value="">-- Sila Pilih Status --</option>
                        <option value="Pandangan undang-undang/Maklum Balas telah dikemukakan /melalui e-mel kepada ………. pada …… Julai 20XX">1. Pandangan undang-undang melalui e-mel</option>
                        <option value="Draf pandangan undang-undang telah dikemukakan kepada Penasihat Undang-Undang pada ….. Mei 20XX">2. Draf dikemukakan kepada PUU</option>
                        <option value="Dalam tindakan untuk menyediakan pandangan undang-undang selepas perbincangan dengan YB PUU/Agensi pada … Mei 20XX">3. Dalam tindakan selepas perbincangan</option>
                        <option value="Dalam tindakan untuk mendapatkan dokumen atau maklum balas daripada Agensi ………. melalui surat/email/percakapan/Whatsapps/telefon bertarikh……. Mei 20XX">4. Menunggu dokumen dari agensi</option>
                        <option value="Dalam tindakan untuk menyediakan pandangan undang-undang atau maklum balas selepas menerima dokumen daripada Agensi ……… bertarikh ……. Mei 20XX">5. Menyediakan pandangan selepas dokumen diterima</option>
                        <option value="Cadangan untuk digugurkan daripada Laporan sehingga mendapat tarikh daripada agensi untuk perbincangan">6. Cadangan digugurkan sementara</option>
                        <option value="Draf Perjanjian telah dikemukakan kepada …………………….. pada …………… September 20XX">7. Draf perjanjian telah dikemukakan</option>
                        <option value="Pandangan undang-undang telah dikemukakan dalam mesyuarat pada …………………………">8. Pandangan dikemukakan dalam mesyuarat</option>
                    </select>
                    <textarea name="status" id="status" class="form-control" rows="4" required>{{ $laporan->status }}</textarea>
                </div>

                <div class="col-md-6">
                    <label for="tarikh_selesai" class="form-label">Tarikh Selesai</label>
                    <input type="date" name="tarikh_selesai" class="form-control" value="{{ $laporan->tarikh_selesai ? $laporan->tarikh_selesai->format('Y-m-d') : '' }}">

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="belum_selesai" id="belum_selesai" value="1"
                            {{ $laporan->belum_selesai ? 'checked' : '' }}>
                        <label class="form-check-label" for="belum_selesai">Belum Selesai</label>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="dirujuk_jpn" id="dirujuk_jpn" value="1"
                            {{ $laporan->dirujuk_jpn ? 'checked' : '' }}>
                        <label class="form-check-label" for="dirujuk_jpn">Dirujuk ke JPN (Ibu Pejabat)</label>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">Simpan Kemaskini</button>
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
