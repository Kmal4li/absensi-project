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
                        <ul>
                            <a href="{{ $file->getUrl() }}" class="btn btn-sm btn-primary">Download {{ $file->name }}</a>
                        </ul>   
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Tidak ada file yang diunggah.</p>
            @endif

            <form action="{{ route('perjalanan.downloadLaporan', $perjalanan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="laporan_keuangan" class="form-label">File Laporan (PDF)</label>
                    <input type="file" id="laporan_keuangan" name="laporan_keuangan" class="form-control" accept="application/pdf">
                    @error('laporan_keuangan') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
