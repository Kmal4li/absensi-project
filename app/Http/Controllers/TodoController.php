<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Models\User;
use App\Models\Attendance;

class TodoController extends Controller
{
    public function index()
{
    $attendances = Attendance::all();
    $todos = Todo::all(); 

    return view('todo.index', compact('attendances', 'todos'));
}

    public function show($id)
    {
        $todo = Todo::findOrFail($id);
        return view('todo.show', compact('todo'));
    }

    public function markAsDone($id)
{
    $todo = Todo::findOrFail($id);
    $todo->update(['status_kegiatan' => 'Selesai']);

    return redirect()->route('todo.show', $id)->with('success', 'Kegiatan telah diselesaikan.');
}


    public function create()
    {
        return view('todo.create');
    }
}
