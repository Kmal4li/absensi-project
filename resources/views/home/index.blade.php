@extends('layouts.home')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-2">
                <div class="card-header">
                    Daftar Absensi Hari Ini
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
            <div class="card shadow-sm">
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
                            <span>{{ auth()->user()->created_at->diffForHumans() }} ({{
                                auth()->user()->created_at->format('d M Y') }})</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mengecek apakah geolocation tersedia di browser
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            // Ambil latitude dan longitude
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            // Tampilkan data lokasi di elemen dengan ID 'geo-location'
            document.getElementById('geo-location').innerHTML = 
                'Latitude: ' + latitude + ', Longitude: ' + longitude;

            // Anda bisa mengirimkan data ini ke server jika diperlukan
            // Misalnya dengan AJAX atau dengan mengirimkan form
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