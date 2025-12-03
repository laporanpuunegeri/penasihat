@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Kalendar Pergerakan Pegawai</h1>
            <p class="text-muted small mb-0">Paparan jadual pergerakan dan status kelulusan.</p>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            {{-- ** NOTA: Butang Cetak Kalendar (PDF) Dikeluarkan dari sini dan dimasukkan ke dalam kalendar JS ** --}}
            <a href="{{ route('pergerakan.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle me-2"></i> Daftar Pergerakan Baharu
            </a>
        </div>
    </div>
    
    {{-- Filter Pegawai (Untuk Boss/CC/YB) --}}
    @php $userRole = strtolower(auth()->user()->role); @endphp

    @if(in_array($userRole, ['cc', 'boss', 'yb']))
        <div class="card mb-4 border-0 shadow-sm" id="filter-card"> 
            <div class="card-body py-3">
                <form method="GET" id="filterForm" class="row g-3 align-items-center">
                    {{-- 1. Penapis Pegawai --}}
                    <div class="col-auto">
                        <label for="pegawai_id" class="col-form-label fw-bold text-secondary">
                            <i class="fas fa-filter me-1"></i> Tapis Pegawai:
                        </label>
                    </div>
                    <div class="col-auto">
                        <select name="pegawai_id" id="pegawai_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Lihat Semua Pegawai --</option>
                            @foreach ($senarai_pegawai as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ request('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Butang Penapis Status Segera --}}
                    <input type="hidden" name="status_filter" id="status_filter_input" value="{{ request('status_filter') }}">

                    <div class="col-auto">
                        @if ($userRole === 'cc' || $userRole === 'boss')
                            <button type="button" 
                                class="btn btn-warning btn-sm shadow-sm {{ request('status_filter') == 'cc_pending' ? 'active' : '' }}" 
                                onclick="toggleStatusFilter('cc_pending')">
                                <i class="fas fa-hourglass-half me-1"></i> Permohonan Belum Disokong
                            </button>
                        @endif

                        @if ($userRole === 'yb' || $userRole === 'boss')
                            <button type="button" 
                                class="btn btn-info btn-sm shadow-sm {{ request('status_filter') == 'yb_pending' ? 'active' : '' }}" 
                                onclick="toggleStatusFilter('yb_pending')">
                                <i class="fas fa-clipboard-check me-1"></i> Permohonan Belum Disahkan
                            </button>
                        @endif

                        {{-- Butang Bersihkan Penapis (Muncul jika ada sebarang filter) --}}
                        @if (request('status_filter') || request('pegawai_id'))
                            <button type="button" 
                                class="btn btn-outline-secondary btn-sm shadow-sm ms-2" 
                                onclick="clearStatusFilter()">
                                <i class="fas fa-times me-1"></i> Reset Semua
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <script>
            function toggleStatusFilter(status) {
                const input = document.getElementById('status_filter_input');
                const form = document.getElementById('filterForm');
                if (input.value === status) { input.value = ''; } else { input.value = status; }
                form.submit();
            }

            function clearStatusFilter() {
                window.location.href = "{{ route('pergerakan.index') }}";
            }
        </script>
    @endif

    {{-- Kalendar --}}
    <div class="card shadow border-0 rounded-3">
        <div class="card-body p-4">
            <div id="calendar" style="min-height: 650px;"></div>
        </div>
    </div>
</div>

{{-- MODAL 1: PERINCIAN PERGERAKAN (POPUP ASAL) --}}
<div class="modal fade" id="pergerakanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            {{-- Header Modal --}}
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    <i class="fas fa-info-circle me-2"></i> Perincian Pergerakan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            {{-- Body Modal --}}
            <div class="modal-body p-0">
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr>
                            <td class="px-4 py-3 text-muted fw-bold" width="35%"><i class="far fa-calendar-alt me-2"></i> Tarikh Mula</td>
                            <td class="px-4 py-3 fw-bold text-dark" id="modalStart"></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-muted fw-bold"><i class="far fa-calendar-check me-2"></i> Tarikh Akhir</td>
                            <td class="px-4 py-3 fw-bold text-dark" id="modalEnd"></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-muted fw-bold"><i class="fas fa-car me-2"></i> Kenderaan</td>
                            <td class="px-4 py-3" id="modalKenderaan"></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-muted fw-bold"><i class="fas fa-route me-2"></i> Tujuan/Destinasi</td>
                            <td class="px-4 py-3" id="modalTujuanDestinasi"></td>
                        </tr>
                         <tr>
                            <td class="px-4 py-3 text-muted fw-bold"><i class="fas fa-user-tie me-2"></i> Pemandu Ditugaskan</td>
                            <td class="px-4 py-3" id="modalPemandu"></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-muted fw-bold"><i class="fas fa-list-ol me-2"></i> No. Kenderaan Rasmi</td>
                            <td class="px-4 py-3" id="modalNoKenderaan"></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-muted fw-bold"><i class="fas fa-sticky-note me-2"></i> Catatan Pemohon</td>
                            <td class="px-4 py-3 text-muted fst-italic" id="modalCatatan"></td>
                        </tr>
                        
                        {{-- Status Section --}}
                        <tr class="table-light border-top">
                            <td class="px-4 py-3 text-muted fw-bold align-middle">Status CC</td>
                            <td class="px-4 py-3"><span id="badgeCC" class="badge rounded-pill px-3 py-2"></span></td>
                        </tr>
                        <tr class="table-light">
                            <td class="px-4 py-3 text-muted fw-bold align-middle">Catatan CC</td>
                            <td class="px-4 py-3 text-muted fst-italic" id="modalCatatanCC"></td>
                        </tr>
                        <tr class="table-light">
                            <td class="px-4 py-3 text-muted fw-bold align-middle">Status YB</td>
                            <td class="px-4 py-3"><span id="badgeYB" class="badge rounded-pill px-3 py-2"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Footer Modal --}}
            <div class="modal-footer bg-light">
                
                {{-- 1. Butang Sokong (CC) - Modal / Direct Link --}}
                <button type="button" id="btnSokong" class="btn btn-primary btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-thumbs-up me-1"></i> Sokong
                </button>
                <a href="#" id="linkSokongDirect" class="btn btn-primary btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-thumbs-up me-1"></i> Sokong
                </a>

                {{-- 2. Butang Tolak (CC) - Modal / Direct Link --}}
                <button type="button" id="btnTolak" class="btn btn-danger btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-times me-1"></i> Tolak
                </button>
                <a href="#" id="linkTolakDirect" class="btn btn-danger btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-times me-1"></i> Tolak
                </a>

                {{-- 3. Butang Lulus (YB) --}}
                <a href="#" id="btnLulus" class="btn btn-success btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-check-circle me-1"></i> Luluskan
                </a>
                
                  {{-- 4. Butang Tolak YB --}}
                <a href="#" id="btnTolakYB" class="btn btn-danger btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-times-circle me-1"></i> Tolak YB
                </a>

                {{-- 5. Butang Cetak --}}
                <a href="#" id="btnCetak" target="_blank" class="btn btn-dark btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-print me-1"></i> Cetak Borang
                </a>

                {{-- Spacer --}}
                <div class="vr mx-2"></div>

                {{-- Butang Padam --}}
                <button type="button" id="deleteBtn" class="btn btn-outline-danger btn-sm" style="display:none;">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: Ulasan CC (Penetapan Kenderaan/Pemandu) --}}
<div class="modal fade" id="ccReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-car-side me-2"></i> Penetapan Kenderaan & Pemandu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            <form id="cc_review_form" action="" method="POST">
                @csrf
                @method('PUT') 
                <input type="hidden" name="pergerakan_id" id="cc_pergerakan_id">
                <input type="hidden" name="action_type" id="cc_action_type">
                
                <div class="modal-body p-4">
                    {{-- Maklumat Asas --}}
                    <h6 class="text-primary fw-bold mb-3">Permohonan: <span id="cc_modal_title" class="text-dark"></span></h6>
                    
                    <div id="penetapan_fields">
                        <p class="text-danger fw-bold" id="reject_warning" style="display:none;">
                            <i class="fas fa-exclamation-triangle me-1"></i> Anda memilih untuk **MENOLAK** permohonan. Penetapan kenderaan di bawah akan diabaikan, dan status YB akan ditolak secara automatik.
                        </p>

                        <div class="row mb-4">
                            {{-- Nombor Kenderaan --}}
                            <div class="col-md-6">
                                <label for="no_kenderaan_cc" class="form-label fw-bold">Nombor Kenderaan Diluluskan</label>
                                <select name="no_kenderaan" id="no_kenderaan_cc" class="form-select">
                                    <option value="" disabled selected>-- Pilih Kenderaan --</option>
                                    <option value="VKQ7083">VKQ7083</option>
                                    <option value="MDE 3329">MDE 3329</option>
                                </select>
                            </div>
                            
                            {{-- Nama Pemandu --}}
                            <div class="col-md-6">
                                <label for="nama_pemandu_cc" class="form-label fw-bold">Nama Pemandu Ditugaskan</label>
                                <select name="nama_pemandu" id="nama_pemandu_cc" class="form-select">
                                    <option value="" disabled selected>-- Pilih Pemandu --</option>
                                    <option value="MOHD MASWAN BIN RASIMAN">MOHD MASWAN BIN RASIMAN</option>
                                    <option value="MUHAMMAD HAZIM BIN KAMSAH">MUHAMMAD HAZIM BIN KAMSAH</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Catatan CC (Ulasan/Sokongan) --}}
                    <div class="mb-3">
                        <label for="catatan_cc_review" class="form-label fw-bold">Catatan CC (Wajib diisi jika menolak)</label>
                        <textarea name="catatan_cc" id="catatan_cc_review" rows="3" class="form-control" placeholder="Sila nyatakan ulasan/sebab penolakan atau sokongan."></textarea>
                    </div>

                </div>

                {{-- Footer Modal CC Review --}}
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal & Tutup</button>
                    <button type="submit" id="final_cc_action_btn" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Sahkan Tindakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- BLOK STYLE UNTUK CETAK (Print to PDF) --}}
<style>
    @media print {
        .sidebar, 
        .topbar, 
        .navbar, 
        .d-flex.justify-content-between.align-items-center.mb-4 > div:last-child, 
        #filter-card,
        .modal, 
        .modal-backdrop,
        .fc-header-toolbar,
        .footer { 
            display: none !important;
        }

        .container-fluid,
        .card.shadow.border-0.rounded-3,
        .card-body {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }

        @page {
            size: A4 landscape; 
            margin: 0.5cm; 
        }
    }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script> 

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const events = {!! json_encode($pergerakan ?? []) !!}; 
    
    // Mendapatkan CSRF Token dari tag meta di layout induk
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;
    
    // --- Logik CSRF di luar eventClick kini dibuang sepenuhnya, hanya pembolehubah dikekalkan ---
    
    // Modal setup
    const pergerakanModalEl = document.getElementById('pergerakanModal');
    const ccReviewModalEl = document.getElementById('ccReviewModal');
    const pergerakanModalInstance = new bootstrap.Modal(pergerakanModalEl);
    const ccReviewModalInstance = new bootstrap.Modal(ccReviewModalEl);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ms',
        height: 'auto',
        contentHeight: 650,
        
        // CUSTOM BUTTONS UNTUK CETAK PDF
        customButtons: {
            printKalendar: {
                text: 'Cetak Kalendar (PDF)',
                click: function() {
                    const view = calendar.currentData.viewApi;
                    const date = view.currentStart; // Ambil tarikh mula pandangan semasa
                    const month = moment(date).format('MM');
                    const year = moment(date).format('YYYY');
                    
                    // Route yang mengendalikan cetak Kalendar Keseluruhan
                    const printUrl = "{{ route('pergerakan.cetak_kalendar_keseluruhan') }}" + 
                                     `?month=${month}&year=${year}`;
                    
                    window.open(printUrl, '_blank');
                }
            }
        },

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'printKalendar dayGridMonth,listWeek' // Tambah custom button di sini
        },

        buttonText: {
            today: 	 'Hari Ini',
            month: 	 'Bulan',
            week: 	 'Minggu',
            day: 	 'Hari',
            list: 	 'Senarai'
        },
        events: events,
        eventDisplay: 'block',
        eventClick: function(info) {
            var eventObj = info.event;
            var props = eventObj.extendedProps;
            var eventId = eventObj.id;
            var currentRole = "{{ strtolower(auth()->user()->role) }}";
            var baseUrl = "{{ url('/pergerakan') }}"; 

            // --- 1. DATA POPULATION ---
            const eventTitleWithVehicle = `[${props.kenderaan.toUpperCase()}] ${eventObj.title}`;
            document.getElementById('modalTitle').innerText = eventTitleWithVehicle;
            
            const masaMula = props.masa_mula ? ' @ ' + props.masa_mula : '';
            const masaAkhir = props.masa_akhir ? ' @ ' + props.masa_akhir : '';
            
            document.getElementById('modalStart').innerText = moment(eventObj.start).format('DD/MM/YYYY') + masaMula;
            document.getElementById('modalEnd').innerText = moment(eventObj.end).subtract(1, 'days').format('DD/MM/YYYY') + masaAkhir;
            
            document.getElementById('modalKenderaan').innerText = props.kenderaan ?? '-';
            
            const tujuan = props.tujuan_penggunaan ?? '-';
            const destinasi = props.destinasi ?? '-';
            const tujuanDestinasiText = (tujuan === '-' && destinasi === '-') ? '-' : `${tujuan} (Ke: ${destinasi})`;
            document.getElementById('modalTujuanDestinasi').innerText = tujuanDestinasiText;
            
            document.getElementById('modalPemandu').innerText = props.nama_pemandu ?? '-';
            document.getElementById('modalNoKenderaan').innerText = props.no_kenderaan ?? '-';
            document.getElementById('modalCatatan').innerText = props.catatan ?? '-';
            document.getElementById('modalCatatanCC').innerText = props.catatan_cc ?? '-'; 


            // --- 2. BADGE STATUS ---
            var badgeCC = document.getElementById('badgeCC');
            if (props.status_cc === 'Sokong') {
                badgeCC.innerHTML = '<i class="fas fa-check-circle me-1"></i> DISOKONG';
                badgeCC.className = 'badge rounded-pill bg-success';
            } else if (props.status_cc && props.status_cc.includes('Tolak')) {
                badgeCC.innerHTML = '<i class="fas fa-times-circle me-1"></i> DITOLAK';
                badgeCC.className = 'badge rounded-pill bg-danger';
            } else {
                badgeCC.innerHTML = '<i class="fas fa-hourglass-half me-1"></i> MENUNGGU';
                badgeCC.className = 'badge rounded-pill bg-warning text-dark';
            }

            var badgeYB = document.getElementById('badgeYB');
            if (props.status_yb === 'Lulus') {
                badgeYB.innerHTML = '<i class="fas fa-check-double me-1"></i> DILULUSKAN';
                badgeYB.className = 'badge rounded-pill bg-success';
            } else if (props.status_yb && props.status_yb.includes('Tolak')) {
                badgeYB.innerHTML = '<i class="fas fa-times-circle me-1"></i> DITOLAK';
                badgeYB.className = 'badge rounded-pill bg-danger';
            } else {
                badgeYB.innerHTML = '<i class="fas fa-hourglass-half me-1"></i> MENUNGGU';
                badgeYB.className = 'badge rounded-pill bg-secondary';
            }

            // --- 3. LOGIK BUTANG WORKFLOW ---
            var btnSokong = document.getElementById('btnSokong');
            var btnTolak = document.getElementById('btnTolak');
            var linkSokongDirect = document.getElementById('linkSokongDirect');
            var linkTolakDirect = document.getElementById('linkTolakDirect');
            var btnLulus = document.getElementById('btnLulus');
            var btnTolakYB = document.getElementById('btnTolakYB');
            var btnCetak = document.getElementById('btnCetak');
            
            // Reset Display
            btnSokong.style.display = 'none'; btnTolak.style.display = 'none';
            linkSokongDirect.style.display = 'none'; linkTolakDirect.style.display = 'none';
            btnLulus.style.display = 'none'; btnTolakYB.style.display = 'none';
            btnCetak.style.display = 'none';
            
            btnSokong.onclick = null; btnTolak.onclick = null;

            // A. Logik CC
            if ((currentRole === 'cc' || currentRole === 'boss') && props.status_cc === 'Pending') {
                
                if (props.kenderaan === 'Kenderaan Pejabat') {
                    // *** KENDERAAN PEJABAT: Buka Modal Review ***
                    document.getElementById('no_kenderaan_cc').value = props.no_kenderaan || '';
                    document.getElementById('nama_pemandu_cc').value = props.nama_pemandu || '';
                    document.getElementById('catatan_cc_review').value = props.catatan_cc || '';

                    btnSokong.onclick = function() {
                        pergerakanModalInstance.hide(); 
                        document.getElementById('cc_pergerakan_id').value = eventId;
                        document.getElementById('cc_modal_title').innerText = eventObj.title;
                        document.getElementById('cc_action_type').value = 'support';
                        
                        document.getElementById('reject_warning').style.display = 'none';
                        document.getElementById('no_kenderaan_cc').setAttribute('required', 'required');
                        document.getElementById('nama_pemandu_cc').setAttribute('required', 'required');
                        document.getElementById('catatan_cc_review').removeAttribute('required');

                        document.getElementById('final_cc_action_btn').className = 'btn btn-success';
                        document.getElementById('final_cc_action_btn').innerHTML = '<i class="fas fa-check-circle me-1"></i> Sokong & Tugaskan';
                        document.getElementById('cc_review_form').action = baseUrl + "/" + eventId + "/cc-review";
                        
                        ccReviewModalInstance.show();
                    };

                    btnTolak.onclick = function() {
                        pergerakanModalInstance.hide();
                        document.getElementById('cc_pergerakan_id').value = eventId;
                        document.getElementById('cc_modal_title').innerText = eventObj.title;
                        document.getElementById('cc_action_type').value = 'reject';

                        document.getElementById('reject_warning').style.display = 'block';
                        document.getElementById('no_kenderaan_cc').removeAttribute('required');
                        document.getElementById('nama_pemandu_cc').removeAttribute('required');
                        document.getElementById('catatan_cc_review').setAttribute('required', 'required');
                        
                        document.getElementById('final_cc_action_btn').className = 'btn btn-danger';
                        document.getElementById('final_cc_action_btn').innerHTML = '<i class="fas fa-times-circle me-1"></i> Tolak Permohonan';
                        document.getElementById('cc_review_form').action = baseUrl + "/" + eventId + "/cc-review";

                        ccReviewModalInstance.show();
                    };
                    
                    btnSokong.style.display = 'inline-block';
                    btnTolak.style.display = 'inline-block';

                } else {
                    // *** KENDERAAN SENDIRI: Direct Link ***
                    linkSokongDirect.href = baseUrl + "/" + eventId + "/lulus-cc";
                    linkTolakDirect.href = baseUrl + "/" + eventId + "/tolak-cc";
                    
                    linkSokongDirect.style.display = 'inline-block';
                    linkTolakDirect.style.display = 'inline-block';
                }
            }

            // B. Logik YB
            if (currentRole === 'yb' && props.status_cc === 'Sokong' && props.status_yb === 'Pending') {
                btnLulus.href = baseUrl + "/" + eventId + "/lulus-yb";
                btnLulus.style.display = 'inline-block';
                
                btnTolakYB.href = baseUrl + "/" + eventId + "/tolak-yb";
                btnTolakYB.style.display = 'inline-block';
            }

            // C. Logik Cetak
            if (props.status_yb === 'Lulus') {
                btnCetak.href = baseUrl + "/cetak/" + eventId;
                btnCetak.style.display = 'inline-block';
            }

            // --- 4. LOGIK PADAM ---
            const deleteBtn = document.getElementById('deleteBtn');
            var newDeleteBtn = deleteBtn.cloneNode(true);
            deleteBtn.parentNode.replaceChild(newDeleteBtn, deleteBtn);
            
            // Tentukan kebenaran padam mutlak (sama seperti di Controller)
            const canForceDelete = (currentRole === 'super_admin' || currentRole === 'boss');

            // Logik Paparan Butang Padam
            if (props.status_yb !== 'Lulus' || canForceDelete) {
                 newDeleteBtn.style.display = 'inline-block';
            } else {
                 newDeleteBtn.style.display = 'none';
            }

            // KOD FETCH DELETE YANG TELAH DIBERSIHKAN
            newDeleteBtn.addEventListener('click', function() {
                if(confirm('Adakah anda pasti ingin memadam rekod ini?\nTindakan ini tidak boleh dikembalikan.')) {
                    fetch(baseUrl + "/" + eventId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken, 
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => {
                        if (!r.ok) {
                            return r.json().then(errorData => {
                                throw new Error(errorData.message || 'Gagal memadam. Ralat pelayan (Status: ' + r.status + ')');
                            });
                        }
                        return r.json();
                    })
                    .then(data => {
                        if(data && data.status === 'success') {
                            info.event.remove(); 
                            pergerakanModalInstance.hide();
                            alert(data.message || 'Rekod berjaya dipadam.');
                        } else if (data) {
                             alert(data.message || 'Gagal memadam. Sila semak kebenaran.');
                        }
                    })
                    .catch(error => {
                        // Tangkap ralat dan paparkan mesej yang lebih tepat
                        alert('Ralat pemadaman: ' + error.message);
                    });
                }
            });

            pergerakanModalInstance.show();
        }
    });

    calendar.render();
});
</script>
@endpush