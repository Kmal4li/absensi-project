<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Perjalanan;

class PerjalananEditForm extends Component
{
    use WithFileUploads;

    public $perjalanan;
    public $file_perjalanan;

    protected $rules = [
        'perjalanan.title' => 'required|string|max:255',
        'perjalanan.date_start' => 'required|date',
        'perjalanan.start_time' => 'required|date_format:H:i',
        'perjalanan.date_end' => 'required|date',
        'perjalanan.end_time' => 'required|date_format:H:i',
        'file_perjalanan' => 'nullable|file|mimes:jpg,png,pdf|max:20480', // Sesuaikan jenis file dan ukuran maksimum
    ];

    public function mount(Perjalanan $perjalanan) 
    {
        $this->perjalanan = $perjalanan;
    }

    public function update()
    {
        $this->validate();
        $this->perjalanan->save();

        // Handle file upload
        if ($this->file_perjalanan) {
            $this->perjalanan->clearMediaCollection('perjalanan');

            $this->perjalanan->addMedia($this->file_perjalanan)->toMediaCollection('perjalanan');
        }

        session()->flash('success', 'Perjalanan berhasil diperbarui!');
        return redirect()->route('perjalanan.index'); 
    }

    public function render()
    {
        return view('livewire.perjalanan-edit-form');
    }
}
