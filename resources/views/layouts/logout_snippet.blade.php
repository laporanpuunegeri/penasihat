<!-- Betul: Guna POST dengan CSRF -->
<a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    Log Keluar
</a>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
