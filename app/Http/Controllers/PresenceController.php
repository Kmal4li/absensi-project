<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Permission;
use App\Models\Presence;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresencesExport;
use App\Exports\AttendanceExport;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PresenceController extends Controller
{
    public function index()
{
    $attendances = Attendance::all()->sortByDesc(function ($attendance) {
        return $attendance->data->is_end ?? 0; 
    })->sortByDesc(function ($attendance) {
        return $attendance->data->is_start ?? 0; 
    });

    return view('presences.index', [
        'attendances' => $attendances,
        'title' => 'Daftar Kehadiran'
    ]);

    $fromDate = $request->input('display-by-date-from');
    $toDate = $request->input('display-by-date-to');

    $query = Attendance::query();

    // Apply date range filter if dates are provided
    if ($fromDate && $toDate) {
        $query->whereBetween('presence_date', [$fromDate, $toDate]);
    }

    $attendanceData = $query->get();

    return view('your.view', compact('attendanceData'));
}

    public function show(Attendance $attendance)
    {
        $attendance->load(['positions', 'presences']);

        // dd($qrcode);
        return view('presences.show', [
            "title" => "Data Detail Kehadiran",
            "attendance" => $attendance,
        ]);

    }

    public function export($id)
    {
        $attendance = Attendance::findOrFail($id);
        $presenceData = $attendance->presences()->get();
        $startDate = request('start_date', now()->toDateString());
        $endDate = request('end_date', now()->toDateString()); 

        return Excel::download(new AttendanceExport($presenceData, $startDate, $endDate), 'attendance-' . $attendance->id . '.xlsx');
    }

    public function showPresenceData(Attendance $attendance)
{
    // Get the date range from request or use today's date as default
    $startDate = request('start_date', now()->toDateString());
    $endDate = request('end_date', now()->toDateString());

    // Mengambil presences dengan relasi user berdasarkan rentang tanggal
    $presences = Presence::with('users') // Ambil semua kehadiran dengan relasi pengguna
        ->where('attendance_id', $attendance->id)
        ->whereBetween('presence_date', [$startDate, $endDate]) // Filter by date range
        ->get();

    // Mengelompokkan data kehadiran berdasarkan tanggal
    $presenceData = $presences->groupBy('presence_date')->map(function ($group) {
        return [
            'presence_date' => $group->first()->presence_date,
            'users' => $group->map(function ($presence) {
                return [
                    'name' => $presence->user->name,
                    'presence_enter_time' => $presence->presence_enter_time,
                    'presence_out_time' => $presence->presence_out_time,
                    'email' => $presence->user->email,
                    'phone' => $presence->user->phone,
                    'position' => $presence->user->position->name,
                ];
            })
        ];
    })->toArray();

    return view('presences.presence', compact('attendance', 'presenceData'));
}



public function getNotPresentEmployees($presences)
{
    $uniquePresenceDates = $presences->unique("presence_date")->pluck('presence_date');
    $uniquePresenceDatesAndCompactTheUserIds = $uniquePresenceDates->map(function ($date) use ($presences) {
        return [
            "presence_date" => $date,
            "user_ids" => $presences->where('presence_date', $date)->pluck('user_id')->toArray()
        ];
    });

    $notPresentData = [];
    foreach ($uniquePresenceDatesAndCompactTheUserIds as $presence) {
        $notPresentData[] = [
            "not_presence_date" => $presence['presence_date'],
            "users" => User::query()
                ->with('position')
                ->onlyEmployees()
                ->whereNotIn('id', $presence['user_ids'])
                ->get()
                ->toArray()
        ];
    }
    return $notPresentData;
}



    public function showQrcode()
    {
        $code = request('code');
        $qrcode = $this->getQrCode($code);

        return view('presences.qrcode', [
            "title" => "Generate Absensi QRCode",
            "qrcode" => $qrcode,
            "code" => $code
        ]);
    }

    public function downloadQrCodePDF()
    {
        $code = request('code');
        $qrcode = $this->getQrCode($code);

        $html = '<img src="' . $qrcode . '" />';
        return Pdf::loadHTML($html)->setWarnings(false)->download('qrcode.pdf');
    }

    public function getQrCode(?string $code): string
    {
        if (!Attendance::query()->where('code', $code)->first())
            throw new NotFoundHttpException(message: "Tidak ditemukan absensi dengan code '$code'.");

        return parent::getQrCode($code);
    }

    public function notPresent(Attendance $attendance)
{
    $byDate = request('display-by-date', now()->toDateString());

    // Ambil presences pada tanggal tertentu
    $presences = Presence::query()
        ->where('attendance_id', $attendance->id)
        ->where('presence_date', $byDate)
        ->get(['presence_date', 'user_id']);

    // Jika tidak ada presensi, tampilkan semua karyawan
    if ($presences->isEmpty()) {
        $notPresentData[] = [
            "not_presence_date" => $byDate,
            "users" => User::query()
                ->with('position')
                ->onlyEmployees()
                ->get()
                ->toArray()
        ];
    } else {
        $notPresentData = $this->getNotPresentEmployees($presences);
    }

    return view('presences.not-present', [
        "title" => "Data Karyawan Tidak Hadir",
        "attendance" => $attendance,
        "notPresentData" => $notPresentData
    ]);
}

public function permissions(Attendance $attendance)
{
    $byDate = request('display-by-date', now()->toDateString());

    $permissions = Permission::query()
        ->with(['user', 'user.position'])
        ->where('attendance_id', $attendance->id)
        ->where('permission_date', $byDate)
        ->get();

    return view('presences.permissions', [
        "title" => "Data Karyawan Izin",
        "attendance" => $attendance,
        "permissions" => $permissions,
        "date" => $byDate
    ]);
}


    public function presentUser(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'user_id' => 'required|string|numeric',
            "presence_date" => "required|date"
        ]);

        $user = User::findOrFail($validated['user_id']);

        $presence = Presence::query()
            ->where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->where('presence_date', $validated['presence_date'])
            ->first();

        // jika data user yang didapatkan dari request user_id, presence_date, sudah absen atau sudah ada ditable presences
        if ($presence || !$user)
            return back()->with('failed', 'Request tidak diterima.');

        Presence::create([
            "attendance_id" => $attendance->id,
            "user_id" => $user->id,
            "presence_date" => $validated['presence_date'],
            "presence_enter_time" => now()->toTimeString(),
            "presence_out_time" => now()->toTimeString()
        ]);

        return back()
            ->with('success', "Berhasil menyimpan data hadir atas nama \"$user->name\".");
    }

    public function acceptPermission(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'user_id' => 'required|string|numeric',
            "permission_date" => "required|date"
        ]);

        $user = User::findOrFail($validated['user_id']);

        $permission = Permission::query()
            ->where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->where('permission_date', $validated['permission_date'])
            ->first();

        $presence = Presence::query()
            ->where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->where('presence_date', $validated['permission_date'])
            ->first();

        // jika data user yang didapatkan dari request user_id, presence_date, sudah absen atau sudah ada ditable presences
        if ($presence || !$user)
            return back()->with('failed', 'Request tidak diterima.');

        Presence::create([
            "attendance_id" => $attendance->id,
            "user_id" => $user->id,
            "presence_date" => $validated['permission_date'],
            "presence_enter_time" => now()->toTimeString(),
            "presence_out_time" => now()->toTimeString(),
            'is_permission' => true
        ]);

        $permission->update([
            'is_accepted' => 1
        ]);

        return back()
            ->with('success', "Berhasil menerima data izin karyawan atas nama \"$user->name\".");
    }

    

}
