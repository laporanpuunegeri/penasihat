@extends('layouts.app')

@section('title', 'Urus Pengguna Sistem')

@section('content')
<div class="container-fluid">
    {{-- Header dan Butang Daftar Baru --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Urus Pengguna Sistem</h1>
        
        {{-- 🔥 GUNA ROUTE 'register' --}}
        <a href="{{ route('register') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-user-plus me-2"></i> Daftar Pengguna Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-4 border-success" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    {{-- Kad Senarai Pengguna --}}
    <div class="card shadow border-0 rounded-3">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">Senarai Pegawai Berdaftar</h6>
            <span class="badge bg-secondary">Jumlah: {{ $users->total() ?? count($users) }}</span>
        </div>
        
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Pegawai</th>
                            <th>Email Rasmi</th>
                            <th>Bahagian</th>
                            <th>Peranan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr>
                                <td>{{ $users->firstItem() ? $users->firstItem() + $index : $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->bahagian ?? '-' }}</td>
                                <td>
                                    @php
                                        $roleBadge = 'secondary';
                                        if (in_array(strtolower($user->role), ['cc', 'yb', 'eo'])) {
                                            $roleBadge = 'info';
                                        } elseif (strtolower($user->role) === 'pa') {
                                            $roleBadge = 'warning';
                                        } elseif (strtolower($user->role) === 'super_admin') {
                                            $roleBadge = 'danger';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $roleBadge }}">{{ strtoupper($user->role) }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $user->id }})">
                                        Padam
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tiada pengguna berdaftar ditemui.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($users, 'links'))
                <div class="d-flex justify-content-end">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(userId) {
        if (confirm("Adakah anda pasti ingin memadam pengguna ini? Tindakan ini tidak boleh dibatalkan.")) {
            fetch(`{{ url('tetapan/pengguna') }}/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload(); 
                } else {
                    alert('Gagal memadam pengguna. Semak kebenaran atau log ralat.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terdapat masalah semasa memadam pengguna.');
            });
        }
    }
</script>
@endpush