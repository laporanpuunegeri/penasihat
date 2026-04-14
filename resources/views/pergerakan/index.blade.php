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
            <a href="{{ route('pergerakan.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle me-2"></i> Daftar Pergerakan Baharu
            </a>
        </div>
    </div>
    
    {{-- Filter Pegawai --}}
    @php $userRole = strtolower(auth()->user()->role); @endphp

    @if(in_array($userRole, ['cc', 'boss', 'yb']))
        <div class="card mb-4 border-0 shadow-sm" id="filter-card"> 
            <div class="card-body py-3">
                <form method="GET" id="filterForm" class="row g-3 align-items-center">
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
                    <input type="hidden" name="status_filter" id="status_filter_input" value="{{ request('status_filter') }}">

                    <div class="col-auto">
                        @if (in_array($userRole, ['cc', 'eo']))
                            <button type="button" class="btn btn-warning btn-sm shadow-sm {{ request('status_filter') == 'cc_pending' ? 'active' : '' }}" onclick="toggleStatusFilter('cc_pending')">
                                <i class="fas fa-hourglass-half me-1"></i> Permohonan Belum Disokong
                            </button>
                        @endif

                        @if ($userRole === 'yb')
                            <button type="button" class="btn btn-info btn-sm shadow-sm {{ request('status_filter') == 'yb_pending' ? 'active' : '' }}" onclick="toggleStatusFilter('yb_pending')">
                                <i class="fas fa-clipboard-check me-1"></i> Permohonan Belum Disahkan
                            </button>
                        @endif

                        @if (request('status_filter') || request('pegawai_id'))
                            <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm ms-2" onclick="clearStatusFilter()">
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
            function clearStatusFilter() { window.location.href = "{{ route('pergerakan.index') }}"; }
        </script>
    @endif

    {{-- Kalendar --}}
    <div class="card shadow border-0 rounded-3">
        <div class="card-body p-4">
            <div id="calendar" style="min-height: 650px;"></div>
        </div>
    </div>
</div>

{{-- MODAL 1: PERINCIAN PERGERAKAN --}}
<div class="modal fade" id="pergerakanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fas fa-info-circle me-2"></i> Perincian Pergerakan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
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
                            <td class="px-4 py-3 text-muted fw-bold"><i class="fas fa-paperclip me-2"></i> Lampiran</td>
                            <td class="px-4 py-3" id="modalLampiran"></td>
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
            <div class="modal-footer bg-light">
                {{-- Butang Workflow --}}
                <button type="button" id="btnSokong" class="btn btn-primary btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-thumbs-up me-1"></i> Sokong / Ulas
                </button>
                
                <button type="button" id="btnTolak" class="btn btn-danger btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-times me-1"></i> Tolak
                </button>

                <a href="#" id="btnLulus" class="btn btn-success btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-check-circle me-1"></i> Luluskan
                </a>
                <a href="#" id="btnTolakYB" class="btn btn-danger btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-times-circle me-1"></i> Tolak YB
                </a>
                
                <a href="#" id="btnCetak" target="_blank" class="btn btn-dark btn-sm px-3 shadow-sm" style="display:none;">
                    <i class="fas fa-print me-1"></i> Cetak Borang
                </a>

                <div class="vr mx-2"></div>
                <button type="button" id="deleteBtn" class="btn btn-outline-danger btn-sm" style="display:none;">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: Ulasan CC (Shared Modal) --}}
<div class="modal fade" id="ccReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-pen-nib me-2"></i> Tindakan & Ulasan CC</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="cc_review_form" action="" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="pergerakan_id" id="cc_pergerakan_id">
                <input type="hidden" name="action_type" id="cc_action_type">
                
                <div class="modal-body p-4">
                    <h6 class="text-primary fw-bold mb-3">Permohonan: <span id="cc_modal_title" class="text-dark"></span></h6>
                    
                    {{-- Warning Tolak --}}
                    <p class="text-danger fw-bold bg-danger-subtle p-2 rounded" id="reject_warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-1"></i> Anda memilih untuk **MENOLAK** permohonan ini.
                    </p>

                    {{-- Penetapan Kenderaan (Hanya untuk Kenderaan Pejabat & Sokong) --}}
                    <div id="penetapan_fields">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="no_kenderaan_cc" class="form-label fw-bold">Nombor Kenderaan Diluluskan <span class="text-danger">*</span></label>
                                <select name="no_kenderaan" id="no_kenderaan_cc" class="form-select">
                                    <option value="" disabled selected>-- Pilih Kenderaan --</option>
                                    <option value="VKQ7083">VKQ7083</option>
                                    <option value="MDE 3329">MDE 3329</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="nama_pemandu_cc" class="form-label fw-bold">Nama Pemandu Ditugaskan <span class="text-danger">*</span></label>
                                <select name="nama_pemandu" id="nama_pemandu_cc" class="form-select">
                                    <option value="" disabled selected>-- Pilih Pemandu --</option>
                                    <option value="MOHD FAIZAL BIN ABU BAKAR">MOHD FAIZAL BIN ABU BAKAR</option>
                                    <option value="RAZALI BIN YAAKOB">RAZALI BIN YAAKOB</option>
                                    <option value="MAL’AZIM BIN OTHMAN">AL’AZIM BIN OTHMAN</option>
                                    <option value="ABU BAKAR BIN OTHMAN">ABU BAKAR BIN OTHMAN</option>
                                    <option value="ABDUL RASHID BIN MAAN">ABDUL RASHID BIN MAAN</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan CC --}}
                    <div class="mb-3">
                        <label for="catatan_cc_review" class="form-label fw-bold">Catatan CC <span id="catatan_required_star" class="text-danger" style="display:none;">*</span></label>
                        <textarea name="catatan_cc" id="catatan_cc_review" rows="3" class="form-control" placeholder="Sila nyatakan ulasan/sebab penolakan atau sokongan."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal & Tutup</button>
                    <button type="submit" id="final_cc_action_btn" class="btn btn-success"><i class="fas fa-check-circle me-1"></i> Sahkan Tindakan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .topbar, .navbar, .d-flex.justify-content-between.align-items-center.mb-4 > div:last-child, #filter-card, .modal, .modal-backdrop, .fc-header-toolbar, .footer { display: none !important; }
        .container-fluid, .card.shadow.border-0.rounded-3, .card-body { padding: 0 !important; margin: 0 !important; width: 100% !important; }
        @page { size: A4 landscape; margin: 0.5cm; }
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;
    
    const pergerakanModalInstance = new bootstrap.Modal(document.getElementById('pergerakanModal'));
    const ccReviewModalInstance = new bootstrap.Modal(document.getElementById('ccReviewModal'));

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', locale: 'ms', height: 'auto', contentHeight: 650,
        customButtons: {
            printKalendar: {
                text: 'Cetak Kalendar (PDF)',
                click: function() {
                    const date = calendar.currentData.viewApi.currentStart;
                    const month = moment(date).format('MM');
                    const year = moment(date).format('YYYY');
                    window.open("{{ route('pergerakan.cetak_kalendar_keseluruhan') }}" + `?month=${month}&year=${year}`, '_blank');
                }
            }
        },
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'printKalendar dayGridMonth,listWeek' },
        buttonText: { today: 'Hari Ini', month: 'Bulan', week: 'Minggu', day: 'Hari', list: 'Senarai' },
        events: events, eventDisplay: 'block',
        
        eventClick: function(info) {
            var eventObj = info.event;
            var props = eventObj.extendedProps;
            var eventId = eventObj.id;
            var currentRole = "{{ strtolower(auth()->user()->role) }}";
            var baseUrl = "{{ url('/pergerakan') }}"; 

           // 1. DATA POPULATION
            const eventTitleWithVehicle = `[${props.kenderaan.toUpperCase()}] ${eventObj.title}`;
            
            document.getElementById('modalTitle').innerHTML = `
                ${eventTitleWithVehicle} <br>
                <small class="fw-normal text-light" style="font-size: 0.85rem; opacity: 0.9;">
                    <i class="fas fa-clock me-1"></i> Didaftar pada: ${props.created_at || 'Tiada Rekod'}
                </small>
            `;
            
            document.getElementById('modalStart').innerText = moment(eventObj.start).format('DD/MM/YYYY') + (props.masa_mula ? ' @ ' + props.masa_mula : '');
            document.getElementById('modalEnd').innerText = moment(eventObj.end).subtract(1, 'days').format('DD/MM/YYYY') + (props.masa_akhir ? ' @ ' + props.masa_akhir : '');
            document.getElementById('modalKenderaan').innerText = props.kenderaan ?? '-';
            
            const tujuanText = (props.tujuan_penggunaan ?? '-') + (props.destinasi ? ` (Ke: ${props.destinasi})` : '');
            document.getElementById('modalTujuanDestinasi').innerText = tujuanText;
            document.getElementById('modalPemandu').innerText = props.nama_pemandu ?? '-';
            document.getElementById('modalNoKenderaan').innerText = props.no_kenderaan ?? '-';
            document.getElementById('modalCatatan').innerText = props.catatan ?? '-';
            document.getElementById('modalCatatanCC').innerText = props.catatan_cc ?? '-';
            
            var lampiranHtml = '<span class="text-muted fst-italic">Tiada Lampiran</span>';
            
            if (props.lampiran) {
                if (props.lampiran.startsWith('data:image')) {
                    lampiranHtml = `
                        <a href="#" onclick="var w=window.open(''); w.document.write('<img src=\\'${props.lampiran}\\' style=\\'width:100%\\'>'); return false;" 
                           class="btn btn-sm btn-info text-white shadow-sm">
                            <i class="fas fa-file-image me-1"></i> Lihat Gambar
                        </a>`;
                } else {
                    lampiranHtml = `
                        <a href="/storage/${props.lampiran}" target="_blank" class="btn btn-sm btn-info text-white shadow-sm">
                            <i class="fas fa-file-alt me-1"></i> Lihat Dokumen
                        </a>`;
                }
            }
            document.getElementById('modalLampiran').innerHTML = lampiranHtml;

            // 2. BADGE STATUS
            const setBadge = (elementId, status, successText) => {
                const el = document.getElementById(elementId);
                if (status === 'Sokong' || status === 'Lulus') {
                    el.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${successText}`; el.className = 'badge rounded-pill bg-success';
                } else if (status && status.includes('Tolak')) {
                    el.innerHTML = '<i class="fas fa-times-circle me-1"></i> DITOLAK'; el.className = 'badge rounded-pill bg-danger';
                } else {
                    el.innerHTML = '<i class="fas fa-hourglass-half me-1"></i> MENUNGGU'; el.className = 'badge rounded-pill bg-warning text-dark';
                }
            };
            setBadge('badgeCC', props.status_cc, 'DISOKONG');
            setBadge('badgeYB', props.status_yb, 'DILULUSKAN');

            // 3. BUTANG WORKFLOW
            var btnSokong = document.getElementById('btnSokong'); var btnTolak = document.getElementById('btnTolak');
            var btnLulus = document.getElementById('btnLulus'); var btnTolakYB = document.getElementById('btnTolakYB');
            var btnCetak = document.getElementById('btnCetak'); var deleteBtn = document.getElementById('deleteBtn');

            [btnSokong, btnTolak, btnLulus, btnTolakYB, btnCetak, deleteBtn].forEach(el => el.style.display = 'none');

           // --- LOGIK CC & EO ---
                if ((currentRole === 'cc' || currentRole === 'eo' || currentRole === 'boss') && props.status_cc === 'Pending') {
                btnSokong.style.display = 'inline-block';
                btnTolak.style.display = 'inline-block';
                
                btnSokong.onclick = () => {
                    pergerakanModalInstance.hide();
                    document.getElementById('cc_pergerakan_id').value = eventId;
                    document.getElementById('cc_modal_title').innerText = eventObj.title;
                    document.getElementById('reject_warning').style.display = 'none';
                    document.getElementById('catatan_cc_review').value = props.catatan_cc || ''; 
                    
                    document.getElementById('final_cc_action_btn').className = 'btn btn-success';
                    document.getElementById('final_cc_action_btn').innerHTML = '<i class="fas fa-check-circle me-1"></i> Sahkan Sokongan';
                    document.getElementById('cc_review_form').action = baseUrl + "/" + eventId + "/cc-review";

                    if (props.kenderaan === 'Kenderaan Sendiri') {
                        document.getElementById('penetapan_fields').style.display = 'none';
                        document.getElementById('cc_action_type').value = 'support_sendiri';
                        document.getElementById('no_kenderaan_cc').removeAttribute('required');
                        document.getElementById('nama_pemandu_cc').removeAttribute('required');
                        
                        document.getElementById('catatan_cc_review').setAttribute('required', 'required');
                        document.getElementById('catatan_required_star').style.display = 'inline';
                        document.getElementById('catatan_cc_review').placeholder = "Sila masukkan catatan sokongan.";
                    } else {
                        document.getElementById('penetapan_fields').style.display = 'block';
                        document.getElementById('cc_action_type').value = 'support';
                        document.getElementById('no_kenderaan_cc').setAttribute('required', 'required');
                        document.getElementById('nama_pemandu_cc').setAttribute('required', 'required');
                        
                        document.getElementById('catatan_cc_review').removeAttribute('required');
                        document.getElementById('catatan_required_star').style.display = 'none';
                        document.getElementById('catatan_cc_review').placeholder = "Ulasan tambahan.";
                    }
                    ccReviewModalInstance.show();
                };

                btnTolak.onclick = () => {
                    pergerakanModalInstance.hide();
                    document.getElementById('cc_pergerakan_id').value = eventId;
                    document.getElementById('cc_modal_title').innerText = eventObj.title;
                    document.getElementById('cc_action_type').value = 'reject';
                    document.getElementById('reject_warning').style.display = 'block';
                    document.getElementById('penetapan_fields').style.display = 'none'; 
                    
                    document.getElementById('no_kenderaan_cc').removeAttribute('required');
                    document.getElementById('nama_pemandu_cc').removeAttribute('required');
                    
                    document.getElementById('catatan_cc_review').setAttribute('required', 'required');
                    document.getElementById('catatan_required_star').style.display = 'inline';
                    document.getElementById('catatan_cc_review').placeholder = "Sila nyatakan sebab penolakan.";
                    
                    document.getElementById('final_cc_action_btn').className = 'btn btn-danger';
                    document.getElementById('final_cc_action_btn').innerHTML = '<i class="fas fa-times-circle me-1"></i> Tolak Permohonan';
                    document.getElementById('cc_review_form').action = baseUrl + "/" + eventId + "/cc-review";
                    ccReviewModalInstance.show();
                };
            }

            // 🔥 LOGIK YB & PA (DIKEMASKINI) 🔥
            // Kalau user tu YB ATAU PA, dan CC dah sokong, tapi belum lulus...
            if ((currentRole === 'yb' || currentRole === 'pa') && props.status_cc === 'Sokong' && props.status_yb === 'Pending') {
                btnLulus.href = baseUrl + "/" + eventId + "/lulus-yb"; 
                btnLulus.style.display = 'inline-block';
                
                btnTolakYB.href = baseUrl + "/" + eventId + "/tolak-yb"; 
                btnTolakYB.style.display = 'inline-block';
            }

            // Logik Cetak & Delete
            if (props.status_yb === 'Lulus') { btnCetak.style.display = 'inline-block'; btnCetak.href = baseUrl + "/cetak/" + eventId; }
            
            const canForceDelete = (currentRole === 'super_admin' || currentRole === 'boss');
            if (props.status_yb !== 'Lulus' || canForceDelete) {
                deleteBtn.style.display = 'inline-block';
                var newDeleteBtn = deleteBtn.cloneNode(true);
                deleteBtn.parentNode.replaceChild(newDeleteBtn, deleteBtn);
                newDeleteBtn.addEventListener('click', function() {
                    if(confirm('Adakah anda pasti ingin memadam rekod ini?\nTindakan ini tidak boleh dikembalikan.')) {
                        fetch(baseUrl + "/" + eventId, {
                            method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                        }).then(r => r.json()).then(data => {
                            if(data.status === 'success') { info.event.remove(); pergerakanModalInstance.hide(); alert(data.message); }
                            else { alert(data.message); }
                        }).catch(e => alert('Ralat: ' + e.message));
                    }
                });
            }

            pergerakanModalInstance.show();
        }
    });
    calendar.render();
});
</script>
@endpush