<div class="p-4 bg-white min-h-screen shadow">
    <div class="mb-6 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Kementerian" class="mx-auto w-24">
        <p class="text-sm font-bold mt-2 leading-tight">KEMENTERIAN PERTANIAN<br>DAN KETERJAMINAN MAKANAN</p>
    </div>

    <ul class="space-y-2 text-blue-800">
        <li>
            <a href="/dashboard" class="flex items-center space-x-2 font-semibold hover:text-blue-600">
                <span>🏠</span><span>Utama</span>
            </a>
        </li>
        <li>
            <a href="/profile" class="flex items-center space-x-2 font-semibold hover:text-blue-600">
                <span>👤</span><span>Profil</span>
            </a>
        </li>
    </ul>

    <hr class="my-4">

    <details open class="mb-4">
        <summary class="cursor-pointer font-bold text-md">📁 Laporan</summary>
        <ul class="ml-4 mt-2 space-y-2 text-blue-700">
            <li><a href="/laporanpandanganundang" class="hover:underline">Laporan Pandangan Undang-Undang</a></li>
            <li><a href="/laporankesmahkamah" class="hover:underline">Laporan Kes Mahkamah</a></li>
            <li><a href="/laporangubalanundang" class="hover:underline">Laporan Gubalan Undang-Undang</a></li>
            <li><a href="/laporanpindaanundang" class="hover:underline">Laporan Pindaan Undang-Undang</a></li>
            <li><a href="/laporansemakanundang" class="hover:underline">Laporan Semakan Undang-Undang</a></li>
            <li><a href="/laporanmesyuarat" class="hover:underline">Laporan Mesyuarat</a></li>
            <li><a href="/kestatatertib" class="hover:underline">Kes Tatatertib</a></li>
            <li><a href="/lainlaintugasan" class="hover:underline">Lain-lain Tugasan</a></li>
        </ul>
    </details>
</div>
