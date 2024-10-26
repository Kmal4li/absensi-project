@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Absensi App</h1>
    <a href="{{ route('home.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    <div class="card">
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
                            <a href="{{ $file->getPath() }}" class="btn btn-sm btn-primary">Download {{ $file->name }}</a>
                        </li>   
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Tidak ada file yang diunggah.</p>
            @endif

            <p><strong>Laporan Keuangan:</strong></p>
            @if ($perjalanan->laporan_keuangan)
                <a href="{{ Storage::url($perjalanan->laporan_keuangan) }}" class="btn btn-sm btn-success">Download Laporan Keuangan</a>
            @else
                <p class="text-muted">Belum ada laporan keuangan yang diunggah.</p>
            @endif

            <form action="{{ route('perjalanan.storeLaporan', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="laporan_keuangan" class="form-label">Unggah File Laporan (PDF)</label>
                    <input type="file" id="laporan_keuangan" name="laporan_keuangan" class="form-control" accept="application/pdf" required>
                    @error('laporan_keuangan') 
                        <span class="text-danger">{{ $message }}</span> 
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan Laporan</button>
            </form>
        </div>
    </div>
</div>
@endsection