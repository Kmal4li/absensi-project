<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Permission;
use App\Models\Presence;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PresenceController extends Controller
{
    
    protected function applyDateFilter($query, $fromDate, $toDate)
    {
        if ($fromDate && $toDate) {
            $query->whereBetween('presence_date', [$fromDate, $toDate]);
        }
        return $query;
    }

    public function index(Request $request)
{
    $presences = Presence::all();
    $fromDate = $request->input('display-by-date-from');
    $toDate = $request->input('display-by-date-to');

    $query = Attendance::query();

    
    $query = $this->applyDateFilter($query, $fromDate, $toDate);

    $attendances = $query->get()->sortByDesc(function ($attendance) {
        
        return ($attendance->data->is_end ?? 0) + ($attendance->data->is_start ?? 0);
    });

    return view('presences.index', [
        'attendances' => $attendances,
        'title' => 'Daftar Kehadiran'
    ]);
}


    public function show(Attendance $attendance)
    {
        $attendance->load(['positions', 'presences']);

        $photoUrl = Storage::url('attendance_photos/' . $attendance->photo);

        $data = [
            
        ];

        return view('presences.show', [
            "title" => "Data Detail Kehadiran",
            "attendance" => $attendance,
            "photo" => $photoUrl,
            "data" => $data,
        ]);
    }

    public function savePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
        ]);

        try {
            $photoData = $request->input('photo');

            if (!preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $photoData)) {
                throw new \Exception('Format foto tidak valid');
            }

            
            preg_match('/^data:image\/(\w+);base64,/', $photoData, $matches);
            $extension = $matches[1] ?? 'png';
            
            $photoData = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
            $photoData = str_replace(' ', '+', $photoData);
            $decodedData = base64_decode($photoData);

            if ($decodedData === false || strlen($decodedData) < 100) {
                throw new \Exception('Gagal mendecode data base64');
            }

            $imageName = 'photo_'.time().'_'.Str::random(8).'.'.$extension;
            $path = 'photos/'. $imageName;
            Storage::disk('public')->put($path, $decodedData);

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil disimpan.',
                'path' => $path, 
                'url' => asset('storage/photos'.$path), 
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
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
        $startDate = request('start_date', now()->toDateString());
        $endDate = request('end_date', now()->toDateString());

        $presences = Presence::with('user')
            ->where('attendance_id', $attendance->id)
            ->whereBetween('presence_date', [$startDate, $endDate])
            ->get();

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
        if (!Attendance::query()->where('code', $code)->first()) {
            throw new NotFoundHttpException(message: "Tidak ditemukan absensi dengan code '$code'.");
        }

        return parent::getQrCode($code);
    }

    public function notPresent(Attendance $attendance)
    {
        $byDate = request('display-by-date', now()->toDateString());

        $presences = Presence::query()
            ->where('attendance_id', $attendance->id)
            ->where('presence_date', $byDate)
            ->get(['presence_date', 'user_id']);

        $notPresentData = $presences->isEmpty() 
            ? [[ "not_presence_date" => $byDate, "users" => User::query()->with('position')->onlyEmployees()->get()->toArray() ]]
            : $this->getNotPresentEmployees($presences);

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
            'user_id' => 'required|numeric',
            "presence_date" => "required|date"
        ]);

        $user = User::findOrFail($validated['user_id']);

        $presence = Presence::query()
            ->where('attendance_id', $attendance->id)
            ->where('user_id', $user->id)
            ->where('presence_date', $validated['presence_date'])
            ->first();

        if ($presence || !$user) {
            return back()->with('failed', 'Request tidak diterima.');
        }

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
            'user_id' => 'required|numeric',
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

        if ($presence || !$user) {
            return back()->with('failed', 'Request tidak diterima.');
        }

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