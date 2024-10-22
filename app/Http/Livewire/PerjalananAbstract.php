<?php

namespace App\Http\Livewire;

use App\Models\Perjalanan;
use App\Models\Position;
use Livewire\Component;

class PerjalananAbstract extends Component
{
    public $perjalanan; 
    public $positions;
    public $position_ids = [];

    protected $rules = [
        'perjalanan.title' => 'required|string|min:6',
        'perjalanan.description' => 'required|string|max:500',
        'perjalanan.date_start' => 'required|date',
        'perjalanan.start_time' => 'required|date_format:H:i',
        'perjalanan.date_end' => 'required|date|after:perjalanan.date_start',
        'perjalanan.end_time' => 'required|date_format:H:i|after:perjalanan.start_time',
        'perjalanan.code' => 'sometimes|nullable|boolean',
        'position_ids' => 'required|array',
        "position_ids.*"  => "required|distinct|numeric",
    ];

    public function mount()
    {
        $this->positions = Position::query()->select(['id', 'name'])->get();
    }

    
    public function save()
    {
        $this->validate();

       
        Perjalanan::create($this->perjalanan);

        
        $this->reset('perjalanan', 'position_ids');

        
        $this->emit('perjalananSaved'); 
    }

    public function render()
    {
        return view('livewire.perjalanan-abstract');
    }
}
