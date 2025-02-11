<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Perjalanan;
use App\Models\Holiday;
use App\Models\Permission;
use App\Models\Presence;
use App\Models\Todo;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $attendances = Attendance::query()
            ->get()
            ->sortByDesc('data.is_end')
            ->sortByDesc('data.is_start');

        $perjalanans = $user->perjalanans;
        $todos = Todo::all();

        return view('home.index', [
            "title" => "Beranda",
            "attendances" => $attendances,
            "perjalanans" => $perjalanans,
            "todos" => $todos
        ]);
    }

    public function show(Attendance $attendance)
    {
        $user = auth()->user();
        
        $presences = Presence::query()
            ->where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->get();

        $isHasEnterToday = $presences->where('presence_date', now()->toDateString())->isNotEmpty();

        $isTherePermission = Permission::query()
            ->where('permission_date', now()->toDateString())
            ->where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->first();

        $data = [
            'is_has_enter_today' => $isHasEnterToday,
            'is_not_out_yet' => $presences->where('presence_out_time', null)->isNotEmpty(),
            'is_there_permission' => (bool) $isTherePermission,
            'is_permission_accepted' => $isTherePermission?->is_accepted ?? false
        ];

        $holiday = $attendance->data->is_holiday_today ? Holiday::query()
            ->where('holiday_date', now()->toDateString())
            ->first() : false;

        $history = Presence::query()
            ->where('user_id', $user->id)
            ->where('attendance_id', $attendance->id)
            ->get();

        $priodDate = CarbonPeriod::create($attendance->created_at->toDateString(), now()->toDateString())
            ->toArray();
        $priodDate = array_slice(array_reverse(array_map(fn($date) => $date->toDateString(), $priodDate)), 0, 30);

        return view('home.show', [
            "title" => "Informasi Absensi Kehadiran",
            "attendance" => $attendance,
            "data" => $data,
            "holiday" => $holiday,
            'history' => $history,
            'priodDate' => $priodDate
        ]);
    }

    public function permission(Attendance $attendance)
    {
        return view('home.permission', [
            "title" => "Form Permintaan Izin",
            "attendance" => $attendance
        ]);
    }

    public function sendEnterPresenceUsingQRCode()
    {
        $code = request('code');
        $attendance = Attendance::query()->where('code', $code)->first();

        if ($attendance && $attendance->data->is_start && $attendance->data->is_using_qrcode) {
            Presence::create([
                "user_id" => auth()->user()->id,
                "attendance_id" => $attendance->id,
                "presence_date" => now()->toDateString(),
                "presence_enter_time" => now()->toTimeString(),
                "presence_out_time" => null
            ]);

            return response()->json([
                "success" => true,
                "message" => "Kehadiran atas nama '" . auth()->user()->name . "' berhasil dikirim."
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "Terjadi masalah pada saat melakukan absensi."
        ], 400);
    }

    public function sendOutPresenceUsingQRCode()
    {
        $code = request('code');
        $attendance = Attendance::query()->where('code', $code)->first();

        if (!$attendance || !$attendance->data->is_end || !$attendance->data->is_using_qrcode) {
            return response()->json([
                "success" => false,
                "message" => "Terjadi masalah pada saat melakukan absensi."
            ], 400);
        }

        $presence = Presence::query()
            ->where('user_id', auth()->user()->id)
            ->where('attendance_id', $attendance->id)
            ->where('presence_date', now()->toDateString())
            ->where('presence_out_time', null)
            ->first();

        if (!$presence) {
            return response()->json([
                "success" => false,
                "message" => "Terjadi masalah pada saat melakukan absensi."
            ], 400);
        }

        $presence->update(['presence_out_time' => now()->toTimeString()]);

        return response()->json([
            "success" => true,
            "message" => "Atas nama '" . auth()->user()->name . "' berhasil melakukan absensi pulang."
        ]);
    }
}
