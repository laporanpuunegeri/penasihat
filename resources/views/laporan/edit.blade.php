<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kemaskini Laporan</h2>
    </x-slot>

    <div class="py-12">
        <form action="{{ route('laporan.update', $laporan) }}" method="POST" class="max-w-xl mx-auto">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block">Tajuk</label>
                <input type="text" name="tajuk" value="{{ $laporan->tajuk }}" class="w-full border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block">Kandungan</label>
                <textarea name="kandungan" class="w-full border rounded" rows="5" required>{{ $laporan->kandungan }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block">Tarikh</label>
                <input type="date" name="tarikh" value="{{ $laporan->tarikh }}" class="w-full border rounded" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Kemaskini</button>
        </form>
    </div>
</x-app-layout>
