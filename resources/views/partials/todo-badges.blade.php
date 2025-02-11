{{-- File: resources/views/partials/todo-badges.blade.php --}}
<span class="badge bg-info">{{ $todo->status }}</span>

@if(optional($attendance->data)->is_holiday_today)
    <span class="badge text-bg-success rounded-pill">Hari Libur</span>
@else
@endif
    