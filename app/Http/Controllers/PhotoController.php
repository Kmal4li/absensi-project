<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function store(Request $request)
    {
        $image = $request->input('image');

        // Menghapus bagian "data:image/jpeg;base64,"
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'photo_' . time() . '.jpg';

        Storage::put('public/storage/photos/' . $imageName, base64_decode($image));

        return response()->json(['message' => 'Foto berhasil disimpan', 'filename' => $imageName]);
    }
}
