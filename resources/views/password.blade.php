@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 bg-white shadow-md rounded px-8 pt-6 pb-8">
    <h2 class="text-xl font-bold mb-4">Reset Kata Laluan</h2>
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <div class="mb-4">
            <label class="block">Kata Laluan Baru</label>
            <input type="password" name="password" class="w-full border px-3 py-2 rounded mt-1">
        </div>
        <div class="mb-4">
            <label class="block">Sahkan Kata Laluan</label>
            <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded mt-1">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Kemaskini</button>
    </form>
</div>
@endsection
