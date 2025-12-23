@extends('layouts.app')

@section('title', 'Urus Pengguna Sistem')

@section('content')
<div class="container-fluid">
    {{-- Header dan Butang Daftar Baru --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Urus Pengguna Sistem</h1>
        
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
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-4 border-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
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
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No.</th>
                            <th style="width: 25%">Nama Pegawai</th>
                            <th style="width: 20%">Email Rasmi</th>
                            <th style="width: 20%">Bahagian</th>
                            <th style="width: 10%">Peranan</th>
                            <th style="width: 20%" class="text-center">Tindakan</th>
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
                                            $roleBadge = 'info text-dark';
                                        } elseif (strtolower($user->role) === 'pa') {
                                            $roleBadge = 'warning text-dark';
                                        } elseif (strtolower($user->role) === 'super_admin') {
                                            $roleBadge = 'danger';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $roleBadge }}">{{ strtoupper($user->role) }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        
                                        {{-- LOGIK: Siapa boleh nampak butang Edit? --}}
                                        @php
                                            $currentUser = Auth::user();
                                            $canEdit = false;

                                            // 1. Jika Admin Besar (Administrator), boleh edit semua
                                            if ($currentUser->role == 'administrator') {
                                                $canEdit = true;
                                            }
                                            // 2. Jika Super Admin, HANYA boleh edit staf NEGERI SAMA
                                            elseif ($currentUser->role == 'super_admin' && $currentUser->negeri == $user->negeri) {
                                                $canEdit = true;
                                            }
                                        @endphp

                                        @if($canEdit)
                                            {{-- BUTANG KEMASKINI (Biru) --}}
                                            {{-- Pastikan route 'tetapan.pengguna.edit' wujud dalam web.php --}}
                                            <a href="{{ route('tetapan.pengguna.edit', $user->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Kemaskini">
                                                <i class="fas fa-edit"></i> Kemaskini
                                            </a>
                                        @endif

                                        {{-- BUTANG PADAM (Merah) --}}
                                        <button class="btn btn-sm btn-danger shadow-sm" onclick="confirmDelete({{ $user->id }})" title="Padam">
                                            <i class="fas fa-trash-alt"></i> Padam
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-50"></i>
                                    Tiada pengguna berdaftar ditemui.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($users, 'links'))
                <div class="d-flex justify-content-end mt-3">
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
            // Paparkan loading (optional user experience improvement)
            // document.body.style.cursor = 'wait';
            
            fetch(`{{ url('tetapan/pengguna') }}/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' // Tambahan header supaya server tahu kita nak JSON
                }
            })
            .then(response => {
                if (response.ok) {
                    // Refresh page bila berjaya
                    window.location.reload(); 
                } else {
                    // Kalau server bagi error msg (cth: tak boleh delete super admin)
                    return response.json().then(data => {
                        throw new Error(data.message || 'Gagal memadam pengguna.');
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Terdapat masalah semasa memadam pengguna.');
            })
            .finally(() => {
                // document.body.style.cursor = 'default';
            });
        }
    }
</script>
@endpush