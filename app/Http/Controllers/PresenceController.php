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
            return $query->whereBetween('presence_date', [$fromDate, $toDate]);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $fromDate = $request->input('display-by-date-from');
        $toDate = $request->input('display-by-date-to');
        $query = Attendance::query();
        $query = $this->applyDateFilter($query, $fromDate, $toDate);

        $attendances = $query->get()->sortByDesc(fn($attendance) => ($attendance->data->is_end ?? 0) + ($attendance->data->is_start ?? 0));

        return view('presences.index', compact('attendances'));
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['positions', 'presences']);
        $photoUrl = $attendance->photo ? Storage::url('attendance_photos/' . $attendance->photo) : null;

        return view('presences.show', compact('attendance', 'photoUrl'));
    }

    public function store(Request $request)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('photos', 'public');

        Presence::create([
            'user_id' => auth()->id(),
            'presence_date' => now()->toDateString(),
            'presence_enter_time' => now()->toTimeString(),
            'photo' => $path, 
        ]);

        return back()->with('success', 'Foto berhasil diunggah.');
    }

    return back()->with('error', 'Gagal mengunggah foto.');
}

public function savePhoto(Request $request)
{
    $request->validate([
        'image' => 'required|string', // Pastikan data dikirim sebagai base64 string
    ]);

    $image = $request->image;  
    $image = str_replace('data:image/jpeg;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    $imageName = 'presence_photos/' . uniqid() . '.jpg';

    Storage::disk('public')->put($imageName, base64_decode($image));

    // Simpan ke database
    $presence = Presence::latest()->first();  
    $presence->photo = $imageName;  
    $presence->save();

    return response()->json(['message' => 'Foto berhasil disimpan', 'photo' => asset('storage/' . $imageName)]);
}

    public function export($id)
    {
        $attendance = Attendance::findOrFail($id);
        return Excel::download(new AttendanceExport($attendance->presences, request('start_date', now()), request('end_date', now())), 'attendance-' . $attendance->id . '.xlsx');
    }

    public function showPresenceData(Attendance $attendance)
    {
        $presences = Presence::with('user')->where('attendance_id', $attendance->id)->whereBetween('presence_date', [request('start_date', now()), request('end_date', now())])->get();
        $presenceData = $presences->groupBy('presence_date')->map(fn($group) => [
            'presence_date' => $group->first()->presence_date,
            'users' => $group->map(fn($presence) => [
                'name' => $presence->user->name,
                'presence_enter_time' => $presence->presence_enter_time,
                'presence_out_time' => $presence->presence_out_time,
                'email' => $presence->user->email,
                'phone' => $presence->user->phone,
                'position' => $presence->user->position->name,
            ])
        ])->toArray();

        return view('presences.presence', compact('attendance', 'presenceData'));
    }

    public function notPresent(Attendance $attendance)
    {
        $byDate = request('display-by-date', now()->toDateString());
        $presentUsers = Presence::where('attendance_id', $attendance->id)->where('presence_date', $byDate)->pluck('user_id');
        $notPresentUsers = User::whereNotIn('id', $presentUsers)->with('position')->onlyEmployees()->get();

        return view('presences.not-present', compact('attendance', 'notPresentUsers', 'byDate'));
    }

    public function permissions(Attendance $attendance)
    {
        $permissions = Permission::where('attendance_id', $attendance->id)->where('permission_date', request('display-by-date', now()->toDateString()))->with(['user', 'user.position'])->get();
        return view('presences.permissions', compact('attendance', 'permissions'));
    }

    public function presentUser(Request $request, Attendance $attendance)
    {
        $validated = $request->validate(['user_id' => 'required|numeric', 'presence_date' => 'required|date']);
        $user = User::findOrFail($validated['user_id']);

        if (Presence::where('attendance_id', $attendance->id)->where('user_id', $user->id)->where('presence_date', $validated['presence_date'])->exists()) {
            return back()->with('failed', 'Request tidak diterima.');
        }

        Presence::create([...$validated, 'presence_enter_time' => now()->toTimeString(), 'presence_out_time' => now()->toTimeString()]);
        return back()->with('success', "Berhasil menyimpan data hadir atas nama \"$user->name\".");
    }

    public function acceptPermission(Request $request, Attendance $attendance)
    {
        $validated = $request->validate(['user_id' => 'required|numeric', 'permission_date' => 'required|date']);
        $user = User::findOrFail($validated['user_id']);
        $permission = Permission::where('attendance_id', $attendance->id)->where('user_id', $user->id)->where('permission_date', $validated['permission_date'])->first();

        if (!$permission || Presence::where('attendance_id', $attendance->id)->where('user_id', $user->id)->where('presence_date', $validated['permission_date'])->exists()) {
            return back()->with('failed', 'Request tidak diterima.');
        }

        Presence::create([...$validated, 'presence_enter_time' => now()->toTimeString(), 'presence_out_time' => now()->toTimeString(), 'is_permission' => true]);
        $permission->update(['is_accepted' => true]);
        return back()->with('success', "Berhasil menerima data izin karyawan atas nama \"$user->name\".");
    }
}
