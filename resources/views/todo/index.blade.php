    @extends('layouts.app')

    @push('style')
    @powerGridStyles
    @endpush

    @section('buttons')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div>
            <a href="{{ route('todo.create') }}" class="btn btn-sm btn-primary">
                <span data-feather="plus-circle" class="align-text-bottom me-1"></span>
                Tambah Data To do list
            </a>
        </div>
    </div>
    @endsection

    @section('content')
    @include('partials.alerts')
    <livewire:todo-table />
    <table class="table table-striped"> 
@endsection

    @push('script')
    <script src="{{ asset('jquery/jquery-3.6.0.min.js') }}"></script>
    @powerGridScripts
    @endpush