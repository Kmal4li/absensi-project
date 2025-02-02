<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerjalananUserTransaksi extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'nama_transaksi', 'jumlah_nominal', 'deskripsi', 'bukti_transaksi'];

    public function perjalanan()
{
    return $this->belongsTo(Perjalanan::class, 'perjalanan_id', 'id'); 
}

}
