<?php

namespace App\Http\Controllers;

use App\Models\PerjalananUserTransaksi;
use App\Models\Perjalanan;
use Illuminate\Http\Request;

class PerjalananUserTransaksiController extends Controller
{
    public function store(Request $request, $id)
    {

        $validated = $request->validate([
            'nama_transaksi' => 'required|string|max:255',
            'jumlah_nominal' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'bukti_transaksi' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $perjalanan = Perjalanan::findOrFail($id);

        $laporan = new PerjalananUserTransaksi();
        $laporan->id = $perjalanan->id; 
        $laporan->nama_transaksi = $validated['nama_transaksi'];
        $laporan->jumlah_nominal = $validated['jumlah_nominal'];
        $laporan->deskripsi = $validated['deskripsi'];

        if ($request->hasFile('bukti_transaksi')) {
            $file = $request->file('bukti_transaksi');
            $path = $file->store('bukti_transaksi', 'public'); 
            $laporan->bukti_transaksi = $path;
        }
        $laporan->save();

        return redirect()->back()->with('success', 'Laporan keuangan berhasil disimpan!');
    }
}

