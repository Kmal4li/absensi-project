<div>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="update">
        <div class="mb-3">
            <label for="title">Nama Perjalanan</label>
            <input type="text" id="title" wire:model.defer="perjalanan.title" class="form-control">
            @error('perjalanan.title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-3">
            <label for="date_start">Tanggal Mulai</label>
            <input type="date" id="date_start" wire:model.defer="perjalanan.date_start" class="form-control">
            @error('perjalanan.date_start') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-3">
            <label for="start_time">Waktu Mulai</label>
            <input type="time" id="start_time" wire:model.defer="perjalanan.start_time" class="form-control">
            @error('perjalanan.start_time') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        
        <div class="mb-3">
            <label for="date_end">Tanggal Selesai</label>
            <input type="date" id="date_end" wire:model.defer="perjalanan.date_end" class="form-control">
            @error('perjalanan.date_end') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="end_time">Waktu Selesai</label>
            <input type="time" id="end_time" wire:model.defer="perjalanan.end_time" class="form-control">
            @error('perjalanan.end_time') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
        <label for="file_perjalanan">Upload File Perjalanan</label>
        <input type="file" wire:model="file_perjalanan" class="form-control" id="file_perjalanan">
        @error('file_perjalanan') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="btn btn-primary">Update Perjalanan</button>
    </form>
</div>
