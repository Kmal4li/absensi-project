@extends('layouts.app')

@section('content')
<div class="d-flex flex-column min-vh-100">
    <div class="row flex-grow-1">
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="fs-6 fw-light">Data Jabatan</h6>
                    <h4 class="fw-bold">{{ $positionCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="fs-6 fw-light">Data Karyawan</h6>
                    <h4 class="fw-bold">{{ $userCount }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="footer text-center py-4 mt-auto">
        <p class="text-muted">&copy; 2024 Siswa SMKN 13 Bandung. All rights reserved.</p>
    </div>
</div>
@endsection
