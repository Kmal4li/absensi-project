<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Definisi relasi dengan User (satu ke banyak)
    public function users()
    {
        return $this->hasMany(User::class, 'id', 'user_id'); // Asumsikan 'user_id' adalah foreign key di tabel presences
    }

    // Jika Anda hanya ingin mendapatkan satu pengguna yang hadir, gunakan relasi belongsTo
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

