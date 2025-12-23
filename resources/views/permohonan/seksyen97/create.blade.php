@extends('layouts.agensi')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Permohonan Baru Seksyen 97 (Borang 6A)</h1>
        <a href="{{ route('permohonan.seksyen97') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Senarai
        </a>
    </div>

    <form action="{{ route('permohonan.seksyen97.store') }}" method="POST">
        @csrf

        <div class="row">

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-primary">
                        <h6 class="m-0 font-weight-bold text-primary">1. Maklumat Pemilik & Tanah</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Pemilik <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemilik" class="form-control" placeholder="Contoh: SATIYASEELAN A/L SINNASAMY" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">No. Kad Pengenalan</label>
                            <input type="text" name="no_kp_pemilik" class="form-control" placeholder="Contoh: 880101-01-5555">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Alamat Pemilik</label>
                            <textarea name="alamat_pemilik" class="form-control" rows="3" placeholder="Alamat lengkap dalam geran"></textarea>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jenis Hakmilik</label>
                                <select name="jenis_hakmilik" class="form-control">
                                    <option value="P.N.">P.N.</option>
                                    <option value="G.M.">G.M.</option>
                                    <option value="H.S.(D)">H.S.(D)</option>
                                    <option value="H.S.(M)">H.S.(M)</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">No. Hakmilik <span class="text-danger">*</span></label>
                                <input type="text" name="no_hakmilik" class="form-control" placeholder="Contoh: 23271" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">No. Lot / P.T.</label>
                                <input type="text" name="no_lot" class="form-control" placeholder="Contoh: 10492">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Mukim</label>
                                <input type="text" name="mukim" class="form-control" placeholder="Contoh: BATU BERENDAM">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Daerah</label>
                            <input type="text" name="daerah" class="form-control" value="MELAKA TENGAH">
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-danger">
                        <h6 class="m-0 font-weight-bold text-danger">2. Butiran Tuntutan (Kewangan)</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Sewa Tahun Semasa (RM)</label>
                                <input type="number" step="0.01" name="sewa_tahun_semasa" id="sewa" class="form-control item-kira" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempoh Tunggakan</label>
                                <input type="text" name="tempoh_tunggakan" class="form-control" placeholder="Cth: 2002-2024">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tunggakan Tahun Lepas (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="jumlah_tunggakan" id="tunggakan" class="form-control item-kira" placeholder="0.00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Denda Lewat / Denda Lain (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="denda" id="denda" class="form-control item-kira" placeholder="0.00">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kos Notis (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text">RM</span>
                                <input type="number" step="0.01" name="kos_notis" id="kos" class="form-control item-kira" value="20.00">
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold text-danger" style="font-size: 1.2em;">JUMLAH BESAR (RM)</label>
                            <input type="number" step="0.01" name="jumlah_besar" id="jumlah_besar" class="form-control font-weight-bold text-danger" style="font-size: 1.5em;" readonly required>
                            <small class="text-muted">*Dikira secara automatik</small>
                        </div>

                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-left-info">
                        <h6 class="m-0 font-weight-bold text-info">3. Pihak Berkepentingan (Bank/Gadaian)</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Bank / Institusi</label>
                            <input type="text" name="nama_bank" class="form-control" placeholder="Biarkan kosong jika tiada">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Bank</label>
                            <textarea name="alamat_bank" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-4 text-right">
                    <button type="submit" class="btn btn-primary btn-lg shadow-lg">
                        <i class="fas fa-save fa-sm"></i> Simpan Permohonan
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    // Bila user taip nombor, dia terus kira jumlah
    const inputs = document.querySelectorAll('.item-kira');
    const totalInput = document.getElementById('jumlah_besar');

    inputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    function calculateTotal() {
        let sewa = parseFloat(document.getElementById('sewa').value) || 0;
        let tunggakan = parseFloat(document.getElementById('tunggakan').value) || 0;
        let denda = parseFloat(document.getElementById('denda').value) || 0;
        let kos = parseFloat(document.getElementById('kos').value) || 0;

        let total = sewa + tunggakan + denda + kos;
        
        // Paparkan 2 tempat perpuluhan
        totalInput.value = total.toFixed(2);
    }
</script>

@endsection