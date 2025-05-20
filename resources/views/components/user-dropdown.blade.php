
<div class="relative group mt-6">
    <button class="text-sm font-semibold text-gray-700 focus:outline-none">
        {{ Auth::user()->name }}
    </button>
    <div class="absolute hidden group-hover:block bg-white border rounded shadow-md mt-2 right-0 w-48 z-50">
        {{-- <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">🔧 Profil Pengguna</a> --}}
        {{-- <a href="{{ route('password.request') }}" class="block px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">🔑 Reset Kata Laluan</a> --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm text-gray-700">🔐 Log Keluar</button>
        </form>
    </div>
</div>
