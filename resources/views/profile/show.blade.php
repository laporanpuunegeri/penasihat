@extends('layouts.app')

@section('content')

{{-- Style Khas Profil --}}
<style>
    .page-header {
        background: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border-left: 5px solid #0f172a; /* Dark Navy Accent */
    }

    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
        height: 100%;
    }

    .avatar-large {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 auto 15px auto;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.2);
    }

    .detail-item {
        padding: 15px;
        border-bottom: 1px solid #f8fafc;
    }
    .detail-item:last-child { border-bottom: none; }

    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }

    .detail-value {
        font-size: 1rem;
        color: #1e293b;
        font-weight: 500;
    }

    .role-badge {
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        background-color: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Profil Saya</h3>
            <p class="text-muted small mb-0">Urus maklumat peribadi dan tetapan akaun anda.</p>
        </div>
        
        <a href="{{ route('profile.edit') }}" class="btn btn-warning shadow-sm text-dark fw-bold px-4">
            <i class="fas fa-edit me-2"></i> Kemaskini Profil
        </a>
    </div>

    <div class="row g-4">
        
        {{-- KOLUM KIRI: KAD IDENTITI --}}
        <div class="col-lg-4">
            <div class="profile-card p-4 text-center">
                <div class="avatar-large">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                
                <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>

                @php
                    $role = Auth::user()->role;
                    $roleName = [
                        'user' => 'User Biasa',
                        'pa' => 'Pembantu Tadbir (PA)',
                        'yb' => 'YB Penasihat'
                    ][$role] ?? ucfirst($role);
                @endphp
                
                <span class="role-badge">
                    <i class="fas fa-user-shield me-1"></i> {{ $roleName }}
                </span>

                <hr class="my-4 text-muted opacity-25">

                <div class="text-start px-2">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tarikh Daftar:</span>
                        <span class="fw-bold small text-dark">{{ Auth::user()->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Status Akaun:</span>
                        <span class="badge bg-success">Aktif</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLUM KANAN: BUTIRAN JAWATAN --}}
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="p-3 border-bottom bg-light">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-briefcase me-2 text-primary"></i> Maklumat Jawatan & Penempatan</h6>
                </div>
                
                <div class="p-3">
                    <div class="row">
                        <div class="col-md-6 detail-item">
                            <div class="detail-label">Jawatan</div>
                            <div class="detail-value">{{ Auth::user()->nama_jawatan ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <div class="detail-label">Gred Jawatan</div>
                            <div class="detail-value">{{ Auth::user()->gred_jawatan ?? '-' }}</div>
                        </div>
                        
                        <div class="col-md-6 detail-item">
                            <div class="detail-label">Bahagian / Unit</div>
                            <div class="detail-value">{{ Auth::user()->bahagian ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 detail-item">
                            <div class="detail-label">Negeri</div>
                            <div class="detail-value">{{ Auth::user()->negeri ?? '-' }}</div>
                        </div>

                        <div class="col-md-12 detail-item">
                            <div class="detail-label">No. Telefon</div>
                            <div class="detail-value">
                                @if(Auth::user()->no_telefon)
                                    <i class="fas fa-phone-alt text-muted me-2"></i> {{ Auth::user()->no_telefon }}
                                @else
                                    <span class="text-muted fst-italic">- Belum dikemaskini -</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection