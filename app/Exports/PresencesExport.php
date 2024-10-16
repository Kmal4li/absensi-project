<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport; 
use Illuminate\Http\Request;

class PresencesExport implements FromCollection, WithHeadings
{
    protected $presenceData;

    public function __construct($presenceData)
    {
        $this->presenceData = $presenceData;
    }

    public function collection()
    {
        return collect($this->presenceData->map(function ($data) {
            return [
                'name' => $data->user->name,
                'presence_enter_time' => $data->presence_enter_time ?? 'Belum Absen',
                'presence_out_time' => $data->presence_out_time ?? 'Belum Absen',
                'email' => $data->user->email,
                'phone' => $data->user->phone,
                'position' => $data->user->position
            ];
        }));
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Waktu Masuk',
            'Waktu Pulang',
            'Email',
            'No HP',
            'Posisi'
        ];
    }
}
