<?php

namespace App\Http\Livewire;

use App\Models\Perjalanan;
use Livewire\Component;
use Livewire\WithFileUploads;

class PerjalananCreateForm extends Component
{
    use WithFileUploads;

    public $title;
    public $date_start;
    public $start_time;
    public $date_end;
    public $end_time;
    public $file_perjalanan;

    protected $rules = [
        'title' => 'required|string',
        'date_start' => 'required|date',
        'start_time' => 'required',
        'date_end' => 'required|date',
        'end_time' => 'required',
        'file_perjalanan' => 'nullable|file|mimes:pdf|max:20480',
    ];

    public function save()
    {
        $this->validate();

        // Simpan data perjalanan
        $perjalanan = Perjalanan::create([
            'title' => $this->title,
            'date_start' => $this->date_start,
            'start_time' => $this->start_time,
            'date_end' => $this->date_end,
            'end_time' => $this->end_time,
        ]);

        if ($this->file_perjalanan) {
            $perjalanan->addMedia($this->file_perjalanan)->toMediaCollection('files');
        }

        session()->flash('success', 'Perjalanan created successfully!');

        // Reset input form setelah simpan
        $this->reset();
    }

    public function render()
    {
        return view('livewire\perjalanan-create-from');
    }
}

