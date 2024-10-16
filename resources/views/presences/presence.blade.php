@extends('layouts.app')

@section('buttons')
<div class="btn-toolbar mb-2 mb-md-0">
    <div>
        <a href="{{ route('presences.show', $attendance->id) }}" class="btn btn-sm btn-light">
            <span data-feather="arrow-left-circle" class="align-text-bottom"></span>
            Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
@include('partials.alerts')

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
                <h5 class="card-title">{{ $attendance->title }}</h5>
                <h6 class="card-subtitle mb-2 text-muted">{{ $attendance->description }}</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-success">Data Kehadiran</span>
                </div>
            </div>
            <div class="col-md-6">
    <form action="" method="get">
        <div class="mb-3">
            <label for="filterStartDate" class="form-label fw-bold">Tampilkan Berdasarkan Tanggal</label>
            <div class="input-group mb-3">
                <input type="date" class="form-control" id="filterStartDate" name="start_date"
                    value="{{ request('start_date') }}">
                <span class="input-group-text">sampai</span>
                <input type="date" class="form-control" id="filterEndDate" name="end_date"
                    value="{{ request('end_date') }}">
                <button class="btn btn-primary" type="submit" id="button-addon1">Tampilkan</button>
            </div>
        </div>
    </form>
</div>


@if (count($presenceData) === 0)
    <small class="text-danger fw-bold">Tidak ada data kehadiran untuk ditampilkan.</small>
@else
    <div>
        @foreach ($presenceData as $data)
        <div class="p-3 rounded bg-light border my-3 d-flex align-items-center justify-content-between">
            <div>Hari: <span class="fw-bold">
                    {{ \Carbon\Carbon::parse($data['presence_date'])->dayName }}
                    {{ \Carbon\Carbon::parse($data['presence_date'])->isCurrentDay() ? '(Hari ini)' : '' }}
                </span>
            </div>
            <div>Tanggal: <span class="fw-bold">{{ $data['presence_date'] }}</span></div>
            <div>Jumlah Hadir: <span class="fw-bold">{{ count($data['users']) }}</span></div>
        </div>
        <a href="{{ route('presences.export', $attendance->id) }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" class="btn btn-sm btn-success">
    <span data-feather="download" class="align-text-bottom"></span>
    Ekspor Excel
</a>


        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Karyawan</th>
                        <th scope="col">Waktu Masuk</th>
                        <th scope="col">Waktu Pulang</th>
                        <th scope="col">Kontak</th>
                        <th scope="col">Posisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['users'] as $user)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ $user['presence_enter_time'] ?? 'Belum Absen' }}</td>
                        <td>{{ $user['presence_out_time'] ?? 'Belum Absen' }}</td>
                        <td>
                            <a href="mailto:{{ $user['email'] }}">{{ $user['email'] }}</a>
                            <span class="fw-bold"> / </span>
                            <a href="tel:{{ $user['phone'] }}">{{ $user['phone'] }}</a>
                        </td>
                        <td>{{ $user['position'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>
@endif

@endsection
