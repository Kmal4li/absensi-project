@extends('layouts.app')

@push('style')
    @powerGridStyles
    <style>
        /* Custom CSS tambahan jika diperlukan */
        .badge {
            margin-right: 0.25rem;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h5 class="card-title">{{ $attendance->title }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $attendance->description }}</h6>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        @include('partials.attendance-badges')
                        <a href="{{ route('presences.permissions', $attendance->id) }}" 
                           class="badge text-bg-info text-decoration-none">
                            Karyawan Izin
                        </a>
                        <a href="{{ route('presences.not-present', $attendance->id) }}" 
                           class="badge text-bg-danger text-decoration-none">
                            Belum Absen
                        </a>
                        <a href="{{ route('presences.presence', $attendance->id) }}" 
                           class="badge text-bg-success text-decoration-none">
                            Kehadiran
                        </a>
                        @if ($attendance->code)
                        <a href="{{ route('presences.qrcode', ['code' => $attendance->code]) }}" 
                           class="badge text-bg-success text-decoration-none">
                            QRCode
                        </a>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <small class="fw-bold text-muted d-block">Range Jam Masuk</small>
                                <span class="text-primary">
                                    {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($attendance->batas_start_time)->format('H:i') }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <small class="fw-bold text-muted d-block">Range Jam Pulang</small>
                                <span class="text-primary">
                                    {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($attendance->batas_end_time)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-2">
                                <small class="fw-bold text-muted d-block">Jabatan / Posisi</small>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse ($attendance->positions as $position)
                                    <span class="badge text-bg-success">{{ $position->name }}</span>
                                    @empty
                                    <span class="text-muted">Tidak ada posisi terkait</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <livewire:presence-table attendanceId="{{ $attendance->id }}"/>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ asset('jquery/jquery-3.6.0.min.js') }}"></script>
    @powerGridScripts
    <script>
        document.addEventListener('livewire:load', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush