<?php

namespace App\Http\Livewire;

use App\Models\Todo;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class TodoCreateForm extends Component
{
    use WithFileUploads;

    public $nama_kegiatan;
    public $deskripsi_kegiatan;
    public $tanggal_kegiatan;
    public $status_kegiatan = 'Belum Diselesaikan';
    public $user_id = [];  
    public $users = [];  

    protected $rules = [
        'nama_kegiatan' => 'required|string|max:255',
        'deskripsi_kegiatan' => 'required|string|max:255',
        'tanggal_kegiatan' => 'nullable|date',
        'status_kegiatan' => 'nullable|string',
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

    Todo::create([
        'nama_kegiatan' => $this->nama_kegiatan,  
        'deskripsi_kegiatan' => $this->deskripsi_kegiatan, 
        'tanggal_kegiatan' => now(),
        'status_kegiatan' => 'Belum Diselesaikan',
    ]);

    session()->flash('success', 'Kegiatan berhasil ditambahkan!');
    
    $this->reset(['nama_kegiatan', 'deskripsi_kegiatan']);
}




    public function render()
    {
        return view('livewire.todo-create-form',  [
            'users' => $this->users, 
        ]);
    }
}