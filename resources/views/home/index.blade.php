@extends('layouts.home')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-2">
                <div class="card-header">
                    Data Absensi Hari Ini
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($attendances as $attendance)
                        <a href="{{ route('home.show', $attendance->id) }}"
                            class="list-group-item d-flex justify-content-between align-items-start py-3">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">{{ $attendance->title }}</div>
                                <p class="mb-0">{{ $attendance->description }}</p>
                            </div>
                            @include('partials.attendance-badges')
                        </a>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-2">
                <div class="card-header">
                    Informasi Karyawan
                </div>
                <div class="card-body">
                    <ul class="ps-3">
                        <li class="mb-1">
                            <span class="fw-bold d-block">Nama : </span>
                            <span>{{ auth()->user()->name }}</span>
                        </li>
                        <li class="mb-1">
                            <span class="fw-bold d-block">Email : </span>
                            <a href="mailto:{{ auth()->user()->email }}">{{ auth()->user()->email }}</a>
                        </li>
                        <li class="mb-1">
                            <span class="fw-bold d-block">No. Telp : </span>
                            <a href="tel:{{ auth()->user()->phone }}">{{ auth()->user()->phone }}</a>
                        </li>
                        <li class="mb-1">
                            <span class="fw-bold d-block">Lokasi : </span>
                            <span id="geo-location"></span>
                        </li>
                        <li class="mb-1">
                            <span class="fw-bold d-block">Bergabung Pada : </span>
                            <span>{{ auth()->user()->created_at->diffForHumans() }} ({{ auth()->user()->created_at->format('d M Y') }})</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
        <div class="card shadow-sm mb-2">
        <div class="card-header">
            Data Perjalanan Dinas
        </div>
        <div class="card-body">
            <ul class="list-group">
                @foreach ($perjalanans as $perjalanan)
                <a href="{{ route('perjalanan.show', $perjalanan->id) }}"
                    class="list-group-item d-flex justify-content-between align-items-start py-3">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">{{ $perjalanan->title }}</div>
                        <p class="mb-0">{{ $perjalanan->description }}</p>
                    </div>
                    @include('partials.perjalanan-badges')
                </a>
                @endforeach
            </ul>
            @if ($perjalanans->isEmpty())
                <p class="text-muted">Tidak ada data perjalanan dinas.</p>
            @endif
        </div>
    </div>

    <div class="col-md-12">
    <div class="card shadow-sm mb-2">
        <div class="card-header">
            Data To Do List
        </div>
        <div class="card-body">
            @if ($todos->isEmpty())
                <p class="text-muted">Tidak ada data To Do List.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($todos as $index => $todo)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $todo->nama_kegiatan }}</td>
                            <td>{{ $todo->deskripsi_kegiatan }}</td>
                            <td>
                                <a href="{{ route('todo.show', $todo->id) }}" class="btn btn-info btn-sm">Lihat</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>


    </div>
</div>
    </div>
</div>
<div class="footer text-center py-4 mt-auto">
    <p class="text-muted">&copy; 2024 Siswa SMKN 13 Bandung. All rights reserved.</p>
</div>
<script>
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            document.getElementById('geo-location').innerHTML = 
                'Latitude: ' + latitude + ', Longitude: ' + longitude;
        }, function(error) {
            document.getElementById('geo-location').innerHTML = 
                'Gagal mendapatkan lokasi: ' + error.message;
        });
    } else {
        document.getElementById('geo-location').innerHTML = 
            'Geolocation tidak tersedia di browser ini.';
    }
</script>

@endsection
