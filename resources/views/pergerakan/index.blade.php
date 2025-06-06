@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Kalendar Pergerakan Pegawai</h1>

    <a href="{{ route('pergerakan.create') }}" class="btn btn-primary mb-3">
        ➕ Daftar Pergerakan Baharu
    </a>

    {{-- Pilihan pegawai (untuk boss & yb) --}}
    @if(auth()->user()->role === 'boss' || auth()->user()->role === 'yb')
        <form method="GET" class="mb-4">
            <label for="pegawai_id" class="form-label">Pilih Pegawai:</label>
            <select name="pegawai_id" id="pegawai_id" class="form-select w-auto d-inline" onchange="this.form.submit()">
                <option value="">-- Semua Pegawai --</option>
                @foreach ($senarai_pegawai as $pegawai)
                    <option value="{{ $pegawai->id }}" {{ request('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                        {{ $pegawai->name }}
                    </option>
                @endforeach
            </select>
        </form>
    @endif

    <div id="calendar" style="min-height: 500px;"></div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ms',
                height: 650,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                events: {!! json_encode($pergerakan ?? []) !!},
                eventDisplay: 'block',
                eventClick: function (info) {
                    const catatan = info.event.extendedProps.catatan ?? '-';

                    alert(
                        '📌 Jenis: ' + info.event.title + '\n' +
                        '🗓 Tarikh: ' + info.event.startStr + '\n' +
                        '📝 Catatan: ' + catatan
                    );
                }
            });

            calendar.render();
        });
    </script>
@endpush
