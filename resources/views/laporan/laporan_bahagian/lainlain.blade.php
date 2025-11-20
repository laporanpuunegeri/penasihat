@php
    $laporanTapis = collect($laporan_lainlain ?? [])->filter(function ($item) use ($bulan, $tahun) {
        return \Carbon\Carbon::parse($item->tarikh_daftar)->month == $bulan 
            && \Carbon\Carbon::parse($item->tarikh_daftar)->year == $tahun;
    });
@endphp

@forelse ($laporanTapis as $index => $item)
    <tr>
        <td class="text-center">{{ $loop->iteration }}</td>
        <td class="text-start">{{ $item->perihal }}</td>
        <td class="text-center">{{ \Carbon\Carbon::parse($item->tarikh_daftar)->format('d/m/Y') }}</td>
        <td class="text-start">{{ $item->tindakan }}</td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-muted text-center fst-italic">
            Tiada laporan tugasan lain-lain direkodkan.
        </td>
    </tr>
@endforelse
