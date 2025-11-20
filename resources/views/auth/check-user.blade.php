<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Reset Kata Laluan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Reset Kata Laluan</h2>

        @if (session('error'))
            <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-700 px-3 py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <p class="text-gray-600 mb-4">Sila masukkan emel dan nombor telefon untuk tetapan semula kata laluan.</p>

        <form method="POST" action="{{ route('custom.password.verify') }}" novalidate>
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       autocomplete="email" autofocus
                       class="mt-1 block w-full border @error('email') border-red-400 @else border-gray-300 @enderror rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
                @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="phone" class="block text-sm font-medium text-gray-700">No Telefon</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                       inputmode="numeric" autocomplete="tel"
                       class="mt-1 block w-full border @error('phone') border-red-400 @else border-gray-300 @enderror rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300">
                @error('phone')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded transition">
                Sahkan & Teruskan
            </button>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-800 underline">Kembali ke Log Masuk</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
