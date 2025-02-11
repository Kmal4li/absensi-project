<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kegiatan',
        'deskripsi_kegiatan',
        'tanggal_kegiatan',
        'status_kegiatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
