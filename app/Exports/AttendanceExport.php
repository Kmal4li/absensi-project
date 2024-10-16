<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $attendance;
    protected $startDate;
    protected $endDate;

    public function __construct($attendance, $startDate, $endDate)
    {
        $this->attendance = $attendance;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Fetch the presences within the specified date range
        return Presence::with('user.position') // Ensure position is loaded
            ->where('attendance_id', $this->attendance->id)
            ->whereBetween('presence_date', [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($presence) {
                return [
                    'Nama Karyawan' => $presence->user->name,
                    'Waktu Masuk' => $presence->presence_enter_time ?? 'Belum Absen',
                    'Waktu Pulang' => $presence->presence_out_time ?? 'Belum Absen',
                    'Email' => $presence->user->email,
                    'Telepon' => $presence->user->phone,
                    'Posisi' => $presence->user->position->name,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Waktu Masuk',
            'Waktu Pulang',
            'Email',
            'Telepon',
            'Posisi',
        ];
    }

    public function export(Attendance $attendance)
{
    $startDate = request('start_date');
    $endDate = request('end_date');

    return Excel::download(new AttendanceExport($attendance, $startDate, $endDate), 'attendance_export.xlsx');
}
}
