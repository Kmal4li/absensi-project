<?php

namespace App\Http\Controllers;

use App\Models\Perjalanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerjalananController extends Controller
{
    public function index()
{
    $perjalanans = Perjalanan::where('id', auth()->user()->id)->paginate(10);
    return view('perjalanan.index', compact('perjalanans'));
}


public function show($id)
{
    $perjalanan = Perjalanan::findOrFail($id); 

    return view('perjalanan.show', compact('perjalanan')); 
}
    
    // Display the create form
    public function create()
    {   
        return view('perjalanan.create');  
    }

    // Download file
    public function download($id)
{
    $perjalanan = Perjalanan::findOrFail($id);
    $mediaItems = $perjalanan->getMedia('files');

    if ($mediaItems->isNotEmpty()) {
        $file = $mediaItems->first();

        if (file_exists($file->getPath())) {
            return response()->download($file->getPath(), $file->original_name);
        } else {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }
    }

    return redirect()->back()->with('error', 'File tidak ditemukan.');
}

public function downloadLaporan($id)
{
    $perjalanan = Perjalanan::findOrFail($id);

    if ($perjalanan->getMedia('files')->isNotEmpty()) {
        $file = $perjalanan->getFirstMedia('files');
        return response()->download($file->getPath(), $file->file_name);
    }

    return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
}



public function save()
{
    $this->validate();

    // Simpan file
    $filePath = $this->file_perjalanan->store('uploads'); 

    Perjalanan::create([
        'title' => $this->title,
        'date_start' => $this->date_start,
        'start_time' => $this->start_time,
        'date_end' => $this->date_end,
        'end_time' => $this->end_time,
        'file_perjalanan' => $filePath, // Simpan path file
    ]);

    session()->flash('success', 'Perjalanan created successfully!');

    $this->reset();
}

// public $laporan_keuangan;
// public function savePerjalanan()
// {

//     $this->validate([
//         'laporan_keuangan' => 'nullable|mimes:pdf|max:10240', 
//     ]);

//     $filePath = $this->laporan_keuangan ? $this->laporan_keuangan->store('laporan_keuangan', 'public') : null;

//     Perjalanan::create([
//         'title' => $this->title,
//         'date_start' => $this->date_start,
//         'start_time' => $this->start_time,
//         'date_end' => $this->date_end,
//         'end_time' => $this->end_time,
//         'laporan_keuangan' => $filePath,
//     ]);

    
//     $this->reset(['title', 'date_start', 'start_time', 'date_end', 'end_time', 'laporan_keuangan']);
// }


   
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'perjalanan.*.title' => 'required|string',
        'perjalanan.*.date_start' => 'required|date',
        'perjalanan.*.start_time' => 'required',
        'perjalanan.*.date_end' => 'required|date',
        'perjalanan.*.end_time' => 'required',
        'perjalanan.*.file_perjalanan' => 'nullable|file|mimes:pdf|max:20480',
    ]);

    foreach ($validatedData['perjalanan'] as $data) {
        $perjalanan = Perjalanan::create([
            'title' => $data['title'],
            'date_start' => $data['date_start'],
            'start_time' => $data['start_time'],
            'date_end' => $data['date_end'],
            'end_time' => $data['end_time'],
            'user_id' => auth()->user()->id, // Menggunakan 'user_id' jika itu nama kolom di tabel Anda
        ]);

        // Menambahkan file perjalanan ke koleksi media jika ada
        if (isset($data['file_perjalanan'])) {
            $perjalanan->addMedia($data['file_perjalanan'])
                ->toMediaCollection('files');
        }
    }

    return redirect()->route('perjalanan.create')->with('success', 'Perjalanan berhasil disimpan!');
}



    // Display the edit form
    public function edit($id)
    {
        $perjalanan = Perjalanan::findOrFail($id);
        return view('perjalanan.edit', compact('perjalanan'));
    }
    
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'perjalanan.title' => 'required|string',
            'perjalanan.date_start' => 'required|date',
            'perjalanan.start_time' => 'required',
            'perjalanan.date_end' => 'required|date',
            'perjalanan.end_time' => 'required',
            'perjalanan.file_perjalanan' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $perjalanan = Perjalanan::findOrFail($id);

       
        $perjalanan->update([
            'title' => $validatedData['perjalanan']['title'],
            'date_start' => $validatedData['perjalanan']['date_start'],
            'start_time' => $validatedData['perjalanan']['start_time'],
            'date_end' => $validatedData['perjalanan']['date_end'],
            'end_time' => $validatedData['perjalanan']['end_time'],
        ]);

        
        if ($request->hasFile('perjalanan.file_perjalanan')) {
            
            $perjalanan->clearMediaCollection('files'); 
            
           
            $perjalanan->addMedia($request->file('perjalanan.file_perjalanan'))
                ->toMediaCollection('files');
        }

        return redirect()->route('perjalanan.edit', $id)->with('success', 'Perjalanan berhasil diupdate!');
    }
}
