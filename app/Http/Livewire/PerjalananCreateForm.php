<?php

namespace App\Http\Livewire;

use App\Models\Perjalanan;
use App\Models\User;
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
    public $user_id = [];  
    public $users = [];    

    protected $rules = [
        'title' => 'required|string',
        'date_start' => 'required|date',
        'start_time' => 'required',
        'date_end' => 'required|date',
        'end_time' => 'required',
        'file_perjalanan' => 'nullable|file|mimes:pdf|max:20480',
        'user_id' => 'nullable|array|min:1',  
        'user_id.*' => 'nullable|exists:users,id', 
    ];

    public function mount()
    {
        $this->users = User::all();
    }

    protected $messages = [
        'user_id.*.exists' => 'The selected user does not exist.',
    ];

    public function save()
{
    $this->validate();

    $perjalanan = Perjalanan::create([
        'title' => $this->title,
        'date_start' => $this->date_start,
        'start_time' => $this->start_time,
        'date_end' => $this->date_end,
        'end_time' => $this->end_time,
    ]);

    if ($this->file_perjalanan) {
        $filePath = $this->file_perjalanan->store('files', 'public');
        $perjalanan->file_path = $filePath;
        $perjalanan->save();
    }

    $perjalanan->users()->sync($this->user_id);

    session()->flash('success', 'Perjalanan created successfully!');

    $this->reset();
}


    public function render()
    {
        return view('livewire.perjalanan-create-form', [
            'users' => $this->users, 
        ]);
    }
}
