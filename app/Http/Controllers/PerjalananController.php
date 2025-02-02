<?php

namespace App\Http\Controllers;

use App\Models\Perjalanan;
use App\Models\PerjalananUserTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerjalananController extends Controller
{
    public function index()
    {
        $perjalananUserTransaksi = PerjalananUserTransaksi::all();
        $perjalanans = Perjalanan::where('id', auth()->user()->id)->paginate(10);
        return view('perjalanan.index', compact('perjalanans', 'perjalananUserTransaksi'));
        
    }

    public function uploadLaporan($id)
    {
        $perjalanan = Perjalanan::findOrFail($id);
        return view('perjalanan.upload_laporan', compact('perjalanan'));
    }

    public function storeLaporan(Request $request, $id)
{
    $validatedData = $request->validate([
        'nama_transaksi' => 'required|string|max:255',
        'jumlah_nominal' => 'required|numeric',
        'deskripsi' => 'nullable|string',
        'bukti_transaksi' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
    ]);

    $fileName = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');

    PerjalananUserTransaksi::create([
        'id' => $id,
        'nama_transaksi' => $validatedData['nama_transaksi'],
        'jumlah_nominal' => $validatedData['jumlah_nominal'],
        'deskripsi' => $validatedData['deskripsi'],
        'bukti_transaksi' => $fileName,
    ]);

    return redirect()->route('perjalanan.show', $id)->with('success', 'Laporan keuangan berhasil ditambahkan.');
}


public function show($id)
{
    $perjalanan = Perjalanan::findOrFail($id);
    $perjalananUserTransaksi = PerjalananUserTransaksi::where('id', $id)->get(); 

    return view('perjalanan.show', [
        'perjalanan' => $perjalanan,
        'perjalananUserTransaksi' => $perjalananUserTransaksi, 
    ]);
}




    public function create()
    {
        return view('perjalanan.create');
    }

    public function downloadLaporan($id)
    {
        $perjalanan = Perjalanan::findOrFail($id);
        $filePath = $perjalanan->laporan_keuangan;

        if (Storage::disk('public')->exists($filePath)) {
            return response()->download(storage_path("app/public/{$filePath}"), 'laporan.pdf', [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
    }

    public function downloadPerjalanan($id)
{
    $perjalanan = Perjalanan::findOrFail($id);
    $file = $perjalanan->getFirstMedia('files');

    if ($file) {
        return response()->download($file->getPath(), $file->file_name);
    }

    return back()->with('error', 'File tidak ditemukan.');
}


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
                'user_id' => auth()->user()->id, 
            ]);

            if (isset($data['file_perjalanan'])) {
                $perjalanan->addMedia($data['file_perjalanan'])
                    ->toMediaCollection('files');
            }
        }

        return redirect()->route('perjalanan.create')->with('success', 'Perjalanan berhasil disimpan!');
    }

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
