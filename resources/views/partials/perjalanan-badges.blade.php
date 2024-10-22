{{-- File: resources/views/partials/perjalanan-badges.blade.php --}}
<span class="badge bg-info">{{ $perjalanan->status }}</span>

@if(optional($attendance->data)->is_holiday_today)
    <span class="badge text-bg-success rounded-pill">Hari Libur</span>
@else

    @if ($perjalanan->date_start && optional($perjalanan->data)->start_time)
        <span class="badge bg-primary rounded-pill">Jam Berangkat</span>
    @elseif($perjalanan->date_end && optional($perjalanan->data)->end_time)
        <span class="badge text-bg-warning rounded-pill">Jam Pulang</span>
    @else
        <span class="badge text-bg-danger rounded-pill">Tutup</span>
    @endif

@endif
    