@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="fw-bold text-uppercase mb-4">Profil Pengguna</h3>

    <div class="card p-4 shadow-sm border-0">
        <table class="table table-borderless">
            <tr><th style="width: 200px;">Nama</th><td>{{ Auth::user()->name }}</td></tr>
            <tr><th>Emel</th><td>{{ Auth::user()->email }}</td></tr>
            <tr><th>No Telefon</th><td>{{ Auth::user()->no_telefon ?? '-' }}</td></tr>
            <tr><th>Negeri</th><td>{{ Auth::user()->negeri ?? '-' }}</td></tr>
            <tr><th>Jawatan</th><td>{{ Auth::user()->nama_jawatan ?? '-' }}</td></tr>
            <tr><th>Gred Jawatan</th><td>{{ Auth::user()->gred_jawatan ?? '-' }}</td></tr>
            <tr><th>Bahagian</th><td>{{ Auth::user()->bahagian ?? '-' }}</td></tr>
            <tr>
                <th>Peranan</th>
                <td>
                    @php
                        $role = Auth::user()->role;
                        $roleName = [
                            'user' => 'User Biasa',
                            'pa' => 'PA',
                            'yb' => 'YB Penasihat'
                        ][$role] ?? ucfirst($role);
                    @endphp
                    {{ $roleName }}
                </td>
            </tr>
            <tr><th>Tarikh Daftar</th><td>{{ Auth::user()->created_at->format('d/m/Y h:i A') }}</td></tr>
        </table>

        <div class="mt-4 text-end">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Kemaskini
            </a>
        </div>
    </div>
</div>
@endsection
