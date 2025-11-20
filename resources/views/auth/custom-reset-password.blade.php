<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Tetapan Semula Kata Laluan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Tetapan Semula Kata Laluan</h2>

        @if (session('status'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-700 px-3 py-2">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('custom.password.update') }}" novalidate>
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Kata Laluan Baharu</label>
                <input type="password" name="password" required
                       class="mt-1 block w-full border @error('password') border-red-400 @else border-gray-300 @enderror rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
                @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Sahkan Kata Laluan</label>
                <input type="password" name="password_confirmation" required
                       class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded transition">
                Simpan Kata Laluan
            </button>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-800 underline">Kembali ke Log Masuk</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
