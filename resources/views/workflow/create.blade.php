<h3>Buat Workflow Baru</h3>
<form action="{{ route('workflow.store', $pergerakan->id) }}" method="POST">
    @csrf
    <label>Status CC:</label>
    <select name="status_cc">
        <option value="Pending">Pending</option>
        <option value="Sokong">Sokong</option>
        <option value="Tolak">Tolak</option>
    </select>

    <label>Status YB:</label>
    <select name="status_yb">
        <option value="Pending">Pending</option>
        <option value="Lulus">Lulus</option>
        <option value="Tolak">Tolak</option>
    </select>

    <label>Catatan CC:</label>
    <textarea name="catatan_cc"></textarea>

    <label>Catatan YB:</label>
    <textarea name="catatan_yb"></textarea>

    <button type="submit">Simpan</button>
</form>
