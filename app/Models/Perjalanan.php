<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

class Perjalanan extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $table = 'perjalanans';

    protected $fillable = [
        'title',
        'date_start',
        'start_time',
        'date_end',
        'end_time',
        'id',
        'file_perjalanan',
        'laporan_keuangan',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'perjalanan_user', 'perjalanan_id', 'user_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('perjalanan') // Nama koleksi
            ->useFallbackUrl('/path/to/fallback/image.jpg'); // Ganti dengan URL fallback yang sesuai
    }
}
