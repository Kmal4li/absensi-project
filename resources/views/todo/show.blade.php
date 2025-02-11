@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Absensi App</h1>
    <a href="{{ route('home.index') }}" class="btn btn-secondary mt-3">Kembali</a>

    <div class="card mb-4">
        <div class="card-header">
            Data Todo List
        </div>
        <div class="card-body">
            <p><strong>Nama Kegiatan:</strong> {{ $todo->nama_kegiatan }}</p>
            <p><strong>Deskripsi:</strong> {{ $todo->deskripsi_kegiatan }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-{{ $todo->status_kegiatan == 'Selesai' ? 'success' : 'warning' }}">
                    {{ $todo->status_kegiatan }}
                </span>
            </p>
            <p><strong>Dibuat Pada:</strong> {{ $todo->created_at }}</p>
            <p><strong>Diupdate Pada:</strong> {{ $todo->updated_at }}</p>

            @if ($todo->status_kegiatan != 'Selesai')
                <form action="{{ route('todo.selesai', $todo->id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">Tandai Selesai</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
