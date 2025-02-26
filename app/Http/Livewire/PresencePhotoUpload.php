<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Presence;
use Illuminate\Support\Facades\Auth;

class PresencePhotoUpload extends Component
{
    use WithFileUploads;

    public $photo;

    public function save()
    {
        $this->validate([
            'photo' => 'image|max:2048',
        ]);

        $path = $this->photo->store('photos', 'public');

        Presence::where('user_id', Auth::id())->update([
            'photo' => $path
        ]);

        $this->emit('savePhoto');
        session()->flash('success', 'Foto berhasil diunggah.');
    }

    public function render()
    {
        return view('livewire.presence-photo-upload');
    }
}
