<?

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Presence;

class PhotoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
        ]);


        $attendance = Attendance::create([
            'photo' => $request->photo
        ]);

        return response()->json([
            'message' => 'Foto berhasil disimpan',
            'data' => $attendance
        ]);
    }

    public function savePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
            'attendance_id' => 'required|exists:attendances,id' 
        ]);
    
        $photoData = $request->input('photo');
        $photoData = str_replace('data:image/png;base64,', '', $photoData);
        $photoData = str_replace(' ', '+', $photoData);
        $imageName = 'photo_' . time() . '.png';
    
    
        \Storage::disk('public')->put('photos/' . $imageName, base64_decode($photoData));
    
        $attendance = Attendance::find($request->attendance_id);
        $attendance->update(['photo' => $imageName]);
    
        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil disimpan.',
            'path' => 'storage/photos/' . $imageName,
        ]);
    }
}
