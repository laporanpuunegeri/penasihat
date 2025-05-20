@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-xl font-bold mb-4">Reset Kata Laluan</h2>
    @if (session('status'))
        <div class="text-green-600 mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                Alamat E-mel
            </label>
            <input id="email" type="email" name="email" required autofocus
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Hantar Pautan Reset
            </button>
        </div>
    </form>
</div>
@endsection
