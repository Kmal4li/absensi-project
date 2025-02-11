<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'presence_date',
        'presence_enter_time',
        'presence_out_time',
        'photo',
        'is_permission'
    ];

    protected $appends = ['photo_url']; 

    

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/photos/' . $this->photo) : null;
    }
    
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
