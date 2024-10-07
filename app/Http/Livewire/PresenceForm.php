<?php

namespace App\Http\Livewire;

use App\Models\Attendance;
use App\Models\Presence;
use Livewire\Component;

class PresenceForm extends Component
{
    public $latitude;
    public $longitude;
    public $allowedLatitude = -6.8785403;
    public $allowedLongitude = 107.5623617;

    public Attendance $attendance;
    public $holiday;
    public $data;

    public function mount(Attendance $attendance, $data, $holiday)
{
    $this->attendance = $attendance;

    \Log::info('Data received: ', $data);

    if (isset($data['latitude']) && isset($data['longitude'])) {
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
    } else {
        $this->latitude = -6.8785403;
        $this->longitude = 107.5623617;
    }

    $this->holiday = $holiday;
}


    public function render()
    {
        return view('livewire.presence-form', [
            'canAttend' => $this->canAttend(),
        ]);
    }

    public function canAttend()
    {
        // Logika untuk memeriksa apakah lokasi berada dalam batas yang diinginkan
        return ($this->latitude == $this->allowedLatitude && $this->longitude == $this->allowedLongitude);
    }

    public function sendEnterPresence()
    {
        // Periksa apakah dapat melakukan absensi dan sudah dalam waktu yang ditentukan
        if ($this->attendance->data->is_start && !$this->attendance->data->is_using_qrcode && $this->canAttend()) { // sama (harus) dengan view
            Presence::create([
                "user_id" => auth()->user()->id,
                "attendance_id" => $this->attendance->id,
                "presence_date" => now()->toDateString(),
                "presence_enter_time" => now()->toTimeString(),
                "presence_out_time" => null
            ]);

            // untuk refresh if statement
            $this->data['is_has_enter_today'] = true;
            $this->data['is_not_out_yet'] = true;

            return $this->dispatchBrowserEvent('showToast', ['success' => true, 'message' => "Kehadiran atas nama '" . auth()->user()->name . "' berhasil dikirim."]);
        }

        return $this->dispatchBrowserEvent('showToast', ['success' => false, 'message' => "Anda tidak dapat melakukan absensi masuk dari lokasi ini."]);
    }

    public function sendOutPresence()
    {
        // jika absensi sudah jam pulang (is_end) dan tidak menggunakan qrcode (kebalikan)
        if (!$this->attendance->data->is_end && $this->attendance->data->is_using_qrcode) // sama (harus) dengan view
            return false;

        $presence = Presence::query()
            ->where('user_id', auth()->user()->id)
            ->where('attendance_id', $this->attendance->id)
            ->where('presence_date', now()->toDateString())
            ->where('presence_out_time', null)
            ->first();

        if (!$presence) // hanya untuk sekedar keamanan (kemungkinan)
            return $this->dispatchBrowserEvent('showToast', ['success' => false, 'message' => "Terjadi masalah pada saat melakukan absensi."]);

        // untuk refresh if statement
        $this->data['is_not_out_yet'] = false;
        $presence->update(['presence_out_time' => now()->toTimeString()]);
        return $this->dispatchBrowserEvent('showToast', ['success' => true, 'message' => "Atas nama '" . auth()->user()->name . "' berhasil melakukan absensi pulang."]);
    }
}
