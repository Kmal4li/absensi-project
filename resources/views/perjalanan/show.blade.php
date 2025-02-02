@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Absensi App</h1>
    <a href="{{ route('home.index') }}" class="btn btn-secondary mt-3">Kembali</a>

    <div class="card mb-4">
        <div class="card-header">
            Data Perjalanan
        </div>
        <div class="card-body">
            <p><strong>Deskripsi:</strong> {{ $perjalanan->description }}</p>
            <p><strong>Tanggal Mulai:</strong> {{ $perjalanan->date_start }}</p>
            <p><strong>Waktu Mulai:</strong> {{ $perjalanan->start_time }}</p>
            <p><strong>Tanggal Selesai:</strong> {{ $perjalanan->date_end }}</p>
            <p><strong>Waktu Selesai:</strong> {{ $perjalanan->end_time }}</p>
            <p><strong>Dibuat Pada:</strong> {{ $perjalanan->created_at }}</p>
            <p><strong>Diupdate Pada:</strong> {{ $perjalanan->updated_at }}</p>

            <p><strong>File Perjalanan:</strong></p>
            @if ($perjalanan->getMedia('files')->isNotEmpty())
                <ul>
                    @foreach ($perjalanan->getMedia('files') as $file)
                        <li>
                            <a href="{{ route('perjalanan.downloadPerjalanan', ['id' => $file->model_id]) }}" class="btn btn-sm btn-primary">Download {{ $file->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Tidak ada file yang diunggah.</p>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            Tambah Laporan Keuangan
        </div>
        <div class="card-body">
        <form action="{{ route('laporan-keuangan.store', ['id' => $perjalanan->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="perjalanan_id" value="{{ $perjalanan->id }}">
                <div class="form-group">
                    <label for="nama_transaksi">Nama Transaksi:</label>
                    <input type="text" id="nama_transaksi" name="nama_transaksi" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="jumlah_nominal">Jumlah Nominal:</label>
                    <input type="number" id="jumlah_nominal" name="jumlah_nominal" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi:</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="bukti_transaksi">Upload Bukti Transaksi:</label>
                    <input type="file" id="bukti_transaksi" name="bukti_transaksi" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Simpan Laporan</button>
            </form>
        </div>
    </div>

    <div class="card">
    <div class="card-header">
        Daftar Laporan Keuangan
    </div>
    <div class="card-body">
        @if ($perjalananUserTransaksi->isEmpty())
            <p class="text-muted">Belum ada laporan keuangan yang diunggah.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Transaksi</th>
                        <th>Jumlah Nominal</th>
                        <th>Deskripsi</th>
                        <th>Bukti Transaksi</th>
                        <th>Dibuat Pada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($perjalananUserTransaksi as $laporan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $laporan->nama_transaksi }}</td>
                            <td>{{ number_format($laporan->jumlah_nominal, 2, ',', '.') }}</td>
                            <td>{{ $laporan->deskripsi }}</td>
                            <td>
                                @if ($laporan->bukti_transaksi)
                                    <a href="{{ asset('storage/' . $laporan->bukti_transaksi) }}" target="_blank" class="btn btn-sm btn-primary">Lihat Bukti</a>
                                @else
                                    <span class="text-muted">Tidak Ada</span>
                                @endif
                            </td>
                            <td>{{ $laporan->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
