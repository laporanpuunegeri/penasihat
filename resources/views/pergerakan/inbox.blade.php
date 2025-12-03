@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Inbox Pergerakan</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($pergerakan->isEmpty())
        <p>Tiada pergerakan untuk disahkan.</p>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pegawai</th>
                <th>Pergerakan</th>
                <th>Tarikh</th>
                <th>Status CC</th>
                <th>Status YB</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pergerakan as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->pegawai->nama ?? '-' }}</td>
                <td>{{ $item->pergerakan }}</td>
                <td>{{ $item->tarikh }}</td>
                <td>{{ $item->status_cc }}</td>
                <td>{{ $item->status_yb }}</td>
                <td>
                    @if(Auth::user()->role == 'CC' && $item->status_cc == 'Pending')
                        <form action="{{ route('pergerakan.sokong', $item->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PUT')
                            <button class="btn btn-success btn-sm">Sokong</button>
                        </form>
                        <form action="{{ route('pergerakan.tolak', $item->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PUT')
                            <button class="btn btn-danger btn-sm">Tolak</button>
                        </form>
                    @elseif(Auth::user()->role == 'YB' && $item->status_cc == 'Sokong' && $item->status_yb == 'Pending')
                        <form action="{{ route('pergerakan.lulus', $item->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PUT')
                            <button class="btn btn-success btn-sm">Lulus</button>
                        </form>
                        <form action="{{ route('pergerakan.tolak', $item->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PUT')
                            <button class="btn btn-danger btn-sm">Tolak</button>
                        </form>
                    @else
                        <span>-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
