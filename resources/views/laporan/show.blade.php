<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan: {{ $laporan->tajuk }}</h2>
    </x-slot>

    <div class="py-12 max-w-xl mx-auto">
        <p><strong>Tarikh:</strong> {{ $laporan->tarikh }}</p>
        <p><strong>Kandungan:</strong></p>
        <p>{{ $laporan->kandungan }}</p>
        <a href="{{ route('laporan.index') }}" class="text-blue-600">← Kembali</a>
    </div>
</x-app-layout>
