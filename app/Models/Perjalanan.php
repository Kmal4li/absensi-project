<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Perjalanan extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'perjalanans';

    protected $fillable = [
        'title',
        'date_start',
        'start_time',
        'date_end',
        'end_time',
        'file_perjalanan',
        'laporan_keuangan',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'perjalanan_user', 'perjalanan_id', 'user_id');
    }

    /**
     * Registrasi koleksi media untuk Spatie Media Library
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('perjalanan')
            ->useFallbackUrl(asset('images/default-perjalanan.jpg')); // Pastikan path gambar valid
    }

    /**
     * Relasi dengan tabel PerjalananUserTransaksi (one-to-many)
     */
    public function laporanKeuangan()
    {
        return $this->hasMany(PerjalananUserTransaksi::class, 'perjalanan_id');
    }
}
